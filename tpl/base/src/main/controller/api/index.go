package api

import (
	"main/controller/base"

	"github.com/gin-gonic/gin"
)

type indexController struct {
}

var IndexController = indexController{}

//发送队列
func (p *indexController) Main(c *gin.Context) {
	ret := base.NewReturn()

	ret.Set("name", "dangzihao")
	ret.Set("mobile", "15101598751")
	c.JSON(200, ret)
}
