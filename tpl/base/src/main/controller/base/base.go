package base

import (
	"errors"
	"fmt"
	"io"
	"main/modules/setting"
	"main/utils"
	"os"
	"path/filepath"
	"strings"

	"github.com/gin-gonic/gin"
)

//http状态码
const (
	HTTP_OK           = 200
	HTTP_REMOVE       = 301
	HTTP_NOTFOUND     = 404
	HTTP_SERVER_ERROR = 500
)

type NormalReturn struct {
	Errno  int                    `json:"errno"`
	Errmsg string                 `json:"errmsg"`
	Data   map[string]interface{} `json:"data"`
}

var (
	globalUploadBasePath    string
	globalUploadBaseURI     string
	globalUploadBaseMaxsize int64
	globalUploadBaseExts    string
)

func NewReturn() *NormalReturn {
	re := new(NormalReturn)
	re.Data = make(map[string]interface{})
	return re
}

func (t *NormalReturn) Error(errno int, format string, params ...interface{}) {
	t.Errno = errno
	t.Errmsg = fmt.Sprintf(format, params...)
}

func (t *NormalReturn) Set(key string, val interface{}) {
	if len(key) > 0 {
		t.Data[key] = val
	}
}

// GetOffsetLimit 获取分页偏移量
func GetOffsetLimit(c *gin.Context) (int, int) {
	page := utils.MustInt(c.Query("page"))
	pageSize := utils.MustInt(c.Query("pagesize"))

	limit := 12
	if pageSize > 0 {
		limit = pageSize
	}

	offset := 0
	if page > 0 {
		offset = (page - 1) * limit
	}

	return offset, limit
}

func Errorf(format string, params ...interface{}) error {
	msg := fmt.Sprintf(format, params...)
	return errors.New(msg)
}

func UploadFile(biz, fieldName string, c *gin.Context, addonPath string) (string, string, error) {
	maxSize := setting.Config.MustInt64("upload."+biz+".maxsize", globalUploadBaseMaxsize)

	file, header, err := c.Request.FormFile(fieldName)
	if err != nil {
		return "", "", err
	}
	if header.Size > maxSize {
		return "", "", Errorf("上传文件超过最大限制")
	}

	filename := header.Filename
	ext := strings.ToLower(filepath.Ext(filename))
	allowExt := strings.Split(setting.Config.MustString("upload."+biz+".exts", globalUploadBaseExts), ",")
	if ext == "" || utils.InSlice(allowExt, ext, "string") == false {
		return "", "", Errorf("只允许上传指定的格式: %s", strings.Join(allowExt, ";"))
	}

	uploadPath := strings.Trim(setting.Config.MustString("upload."+biz+".path", ""), "/")
	if uploadPath == "" {
		uploadPath = biz
	}

	uploadPath = globalUploadBasePath + "/" + uploadPath
	uploadPath, err = filepath.Abs(uploadPath)
	if err != nil {
		return "", "", Errorf("上传目录转化失败: %s", err)
	}
	if addonPath != "" {
		addonPath = "/" + strings.Trim(addonPath, "/")
	}

	err = utils.MkdirAll(uploadPath + addonPath)
	if err != nil {
		return "", "", Errorf("上传目录创建失败: %s", err)
	}
	dstPath := uploadPath + addonPath + "/" + utils.RandFileName() + ext
	dst, err := os.Create(dstPath)
	if err != nil {
		return "", "", Errorf("文件创建失败: %s", err)
	}
	defer dst.Close()
	_, err = io.Copy(dst, file)
	if err != nil {
		return "", "", Errorf("文件写入失败: %s", err)
	}

	uploadURI := strings.Trim(setting.Config.MustString("upload."+biz+".uri", ""), "/")
	if uploadURI == "" {
		uploadURI = biz
	}
	uploadURI = globalUploadBaseURI + "/" + uploadURI
	if len(uploadURI) == 0 {
		return dstPath, "", nil
	}
	uploadRelativeURI := dstPath[len(uploadPath):]

	return dstPath, uploadURI + uploadRelativeURI, nil
}

// 获取表单所有参数
func Args(c *gin.Context) []byte {
	if c.ContentType() == "multipart/form-data" {
		c.Request.ParseMultipartForm(128)
	} else {
		c.Request.ParseForm()
	}
	args, _ := ffjson.Marshal(c.Request.Form)
	return args
}

// 接收get或者post参数，优先get
func I(c *gin.Context, key string) string {
	return c.DefaultQuery(key, c.PostForm(key))
}

// 返回类型动态处理方法, exts 默认第一个参数是返回状态码，可不填写
func Output(c *gin.Context, response interface{}, exts ...int) {
	transferType := c.Request.Header.Get("X-Transfer-Type") // 获取header定制返回值类型
	code := 200                                             // 默认返回状态码
	if len(exts) > 1 {
		code = exts[0]
	}

	switch transferType {
	case "json":
		c.JSON(code, response)
	case "proto":
	default:
		c.ProtoBuf(code, response)
	}
}

func init() {
	globalUploadBasePath = setting.Config.MustString("upload.base.path", "../storage/upload/")
	globalUploadBaseURI = setting.Config.MustString("upload.base.uri", "http://upload.dzhcool.com/")
	globalUploadBaseMaxsize = setting.Config.MustInt64("upload.base.maxsize", 5242800)
	globalUploadBaseExts = setting.Config.MustString("upload.base.exts", ".jpg,.jpeg,.png")
}
