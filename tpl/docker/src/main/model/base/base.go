package base

import (
	"fmt"
	"log"
	"main/modules/logkit"
	"main/modules/setting"
	"os"
	"path"
	"strings"
	"time"

	"github.com/jinzhu/gorm"
	_ "github.com/jinzhu/gorm/dialects/mysql"
)

type DbConfig struct {
	Type, Host, Name, User, Passwd, Path, SSLMode, Port, Charset string
}

func Errorf(format string, a ...interface{}) error {
	if len(a) > 0 {
		return fmt.Errorf(format, a...)
	}
	return fmt.Errorf(format)
}

func LoadConfigs(name string) *DbConfig {
	config := new(DbConfig)
	config.Host = setting.Config.MustString("db."+name+".host", "")
	config.Name = setting.Config.MustString("db."+name+".name", "")
	config.User = setting.Config.MustString("db."+name+".user", "")
	config.Passwd = setting.Config.MustString("db."+name+".pass", "")
	config.Port = setting.Config.MustString("db."+name+".port", "")
	config.Charset = setting.Config.MustString("db."+name+".charset", "utf8")
	return config
}

func setLogger(db *gorm.DB, date string) {
	logpath := strings.TrimRight(setting.Config.MustString("orm.logpath", "data/log"), "/") + "/"
	logfile := logpath + "orm-" + date + ".log"
	os.MkdirAll(path.Dir(logfile), os.ModePerm)
	f, err := os.OpenFile(logfile, os.O_CREATE|os.O_RDWR|os.O_APPEND, 0666)
	if err == nil {
		db.SetLogger(log.New(f, "[orm]", 0))
	}
}

func getEngine(config *DbConfig) (*gorm.DB, error) {
	cnnstr := ""
	if config.Host[0] == '/' { // looks like a unix socket
		cnnstr = fmt.Sprintf("%s:%s@unix(%s:%s)/%s?charset=%s&timeout=3s&parseTime=true&loc=Local",
			config.User, config.Passwd, config.Host, config.Port, config.Name, config.Charset)
	} else {
		cnnstr = fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?charset=%s&timeout=3s&parseTime=true&loc=Local",
			config.User, config.Passwd, config.Host, config.Port, config.Name, config.Charset)
	}
	logkit.Infof("%s", cnnstr)
	return gorm.Open("mysql", cnnstr)
}

func NewEngine(config *DbConfig) (*gorm.DB, error) {
	db, err := getEngine(config)
	if err != nil {
		return nil, fmt.Errorf("Fail to connect to database: %v", err)
	}

	// 关闭tableName自动复数
	db.SingularTable(true)

	// 默认不打印日志
	db.LogMode(false)

	// 设置日志
	if os.Getenv("EYE_ENV") != setting.EYE_ONLINE {
		// 设置日志
		db.LogMode(true)
		date := time.Now().Format("2006-01-02")
		setLogger(db, date)
	}

	return db, nil
}
