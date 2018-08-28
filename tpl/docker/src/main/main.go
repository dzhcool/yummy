package main

import (
	_ "main/model"
	"main/modules/setting"
	"main/routers"

	"github.com/gin-gonic/gin"
)

func main() {
	r := gin.New()
	routers.Register(r)

	r.Run(":" + setting.Config.MustString("http.port", "8080"))
}
