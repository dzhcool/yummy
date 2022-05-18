package middleware

import (
	"main/modules/setting"

	"github.com/gin-gonic/gin"
)

func init() {

}

func Initializes(r *gin.Engine) {
	if setting.AppEnv == setting.EYE_ONLINE {
		gin.SetMode(gin.ReleaseMode)
	}
	r.MaxMultipartMemory = 60 * 1024 * 1024
	r.Use(gin.Logger())
	r.Use(gin.Recovery())
}
