package routers

import (
	"main/controller/api"
	"main/controller/base"
	"main/middleware"
	"net/http"

	"github.com/gin-gonic/gin"
)

func Register(r *gin.Engine) {
	middleware.Initializes(r)

	// r.Static("/static", os.Getenv("SYS_PATH")+"/src/unique/static")
	// r.LoadHTMLGlob(os.Getenv("SYS_PATH") + "/src/unique/" + "templates/*.html")

	r.GET("/", func(c *gin.Context) {
		ret := base.NewReturn()

		ret.Set("data", "It Works")
		c.JSON(200, ret)
	})

	r.GET("/favicon.ico", func(c *gin.Context) {
		c.String(200, "")
	})

	_index := r.Group("/index")
	{
		_index.GET("/main", api.IndexController.Main)
	}

	// protobuf测试接口
	_rest := r.Group("/rest")
	{
		_rest.GET("/get", api.RestController.Get)
		_rest.GET("/lists", api.RestController.Lists)
	}

	_admin := r.Group("/admin").Use(middleware.Jwt())
	{
		_admin.GET("/main", func(c *gin.Context) {
			c.String(http.StatusOK, "It works")
		})
	}
}
