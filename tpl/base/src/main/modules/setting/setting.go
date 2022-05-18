package setting

import (
	"main/utils"
	"os"
)

// 线上环境
const EYE_ONLINE = "online"

var (
	AppName    string
	AppVersion string
	AppDebug   bool
	AppEnv     string
)

func init() {
	AppEnv = os.Getenv("EYE_ENV")
	if AppEnv == "" {
		AppEnv = "dev"
	}
	if AppEnv != EYE_ONLINE {
		AppDebug = true
	}
	if v, err := Config.GetBool("debug"); err == nil {
		AppDebug = v
	}
	if v, err := Config.GetBool("app.debug"); err == nil {
		AppDebug = v
	}

	AppName = Config.MustString("app.name", "AppName")
	AppVersion = version()
}

func version() string {
	ver, err := utils.ReadFile(os.Getenv("EYE_ROOT") + "/conf/version.txt")
	if err != nil {
		ver = []byte("version.txt not exist")
	}
	return string(ver)
}
