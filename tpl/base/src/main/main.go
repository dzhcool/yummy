package main

import (
	_ "main/model"
	"main/modules/setting"
	"main/routers"

	"github.com/gin-gonic/gin"
)

func main() {
	if setting.AppEnv == setting.EYE_ONLINE {
		gin.SetMode(gin.ReleaseMode)
	}

	r := gin.New()
	routers.Register(r)

	r.Run(":" + setting.Config.MustString("http.port", "8080"))
}
