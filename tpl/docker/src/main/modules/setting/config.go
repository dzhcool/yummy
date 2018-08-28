package setting

import (
	"log"
	"os"

	"github.com/safeie/goconfig"
)

type cfg struct {
	config *goconfig.ConfigFile
	env    string
}

var Config = &cfg{}

func (c *cfg) Set(key, value string) {
	c.config.AddOption(c.env, key, value)
}

func (c *cfg) Remove(key string) {
	c.config.RemoveOption(c.env, key)
}

func (c *cfg) Handle() *goconfig.ConfigFile {
	return c.config
}

func (c *cfg) GetString(key string) (string, error) {
	val, err := c.config.GetString(c.env, key)
	if err != nil {
		val, err = c.config.GetString("default", key)
	}
	return val, err
}

func (c *cfg) GetInt(key string) (int, error) {
	val, err := c.config.GetInt(c.env, key)
	if err != nil {
		val, err = c.config.GetInt("default", key)
	}
	return val, err
}

func (c *cfg) GetInt64(key string) (int64, error) {
	val, err := c.config.GetInt64(c.env, key)
	if err != nil {
		val, err = c.config.GetInt64("default", key)
	}
	return val, err
}

func (c *cfg) GetFloat(key string) (float64, error) {
	val, err := c.config.GetFloat(c.env, key)
	if err != nil {
		val, err = c.config.GetFloat("default", key)
	}
	return val, err
}

func (c *cfg) GetBool(key string) (bool, error) {
	val, err := c.config.GetBool(c.env, key)
	if err != nil {
		val, err = c.config.GetBool("default", key)
	}
	return val, err
}

func (c *cfg) MustString(key string, value string) string {
	val, err := c.config.GetString(c.env, key)
	if err != nil || val == "" {
		val = c.config.MustString("default", key, value)
	}
	return val
}

func (c *cfg) MustInt(key string, value int) int {
	val, err := c.config.GetInt(c.env, key)
	if err != nil || val == 0 {
		val = c.config.MustInt("default", key, value)
	}
	return val
}

func (c *cfg) MustInt64(key string, value int64) int64 {
	val, err := c.config.GetInt64(c.env, key)
	if err != nil || val == 0 {
		val = c.config.MustInt64("default", key, value)
	}
	return val
}

func (c *cfg) MustFloat(key string, value float64) float64 {
	val, err := c.config.GetFloat(c.env, key)
	if err != nil || val == 0.0 {
		val = c.config.MustFloat("default", key, value)
	}
	return val
}

func (c *cfg) MustBool(key string, value bool) bool {
	val, err := c.config.GetBool(c.env, key)
	if err != nil {
		val = c.config.MustBool("default", key, value)
	}
	return val
}

func init() {
	Config.env = os.Getenv("EYE_ENV")
	if Config.env == "" {
		Config.env = "default"
	}

	var err error
	file := "conf/app.ini"
	dir := os.Getenv("EYE_ROOT")
	if IsExist(file) == false && dir != "" {
		file = dir + "/" + file
	}
	Config.config, err = goconfig.ReadConfigFile(file)
	if err != nil {
		log.Fatal("无法加载配置文件:%s", err)
		// init an empty config
		Config.config = goconfig.NewConfigFile()
	}
}
