**本程序 forked from https://github.com/msbranco/goconfig**

###程序的特性：

1. 支持linux下最为传统的配置文件写法
2. 支持多个section片段
3. 值配置可以使用等号和冒号“=:”
4. 布尔值支持多种格式，包括：y/n,yes/no,true/false,no/off
5. 支持使用井号和分号“#;”做注释
6. 可选的值引号包裹
7. 自动忽略空行
8. 支持系统环境变量 {{ENV:xxx}}
9. 添加变量支持，{{section.val}} 或者 {{.val}} 获取当前section的内容

###程序中已经提交了测试用的配置文件示例，如：

    ;some comments
    [redis]
    host = 192.168.1.80
    port = 6379
    redisAddr = {{.host}}:{{.port}}
    redisDb = 0
    redisList = "ltest"
    
    [log]
    logOpen = no
    logFile = "/var/log/test.log"
    logDays = 14
    logSize = 1.5
    logEnv = {{ENV:OS_TEST}}

###使用非常简单：

    package main

	import (
		"fmt"
		"github.com/9466/goconfig"
	)

	func main() {
		c, err := goconfig.ReadConfigFile("t.conf")
		if err != nil {
			fmt.Println(err.Error())
		}
		//  fmt.Println(c)
		sv, err := c.GetString("redis", "redisAddr")
		fmt.Println(sv)
	}
    
**读取字符串：**

    c.GetString(section, option string) (string, error)
    c.MustString(section, option string, defaultValue string) string
    
**读取整数值：**

    c.GetInt(section string, option string) (int, error)
    c.GetInt64(section string, option string) (int64, error)
    
    c.MustInt(section string, option string, defaultValue int) int
    c.MustInt64(section string, option string, defaultValue int64) int64
    
**读取浮点数：**

    c.GetFloat(section string, option string) (float64, error)
    c.MustFloat(section string, option string, defaultValue float64) float64
    
**读取布尔值：**

    c.GetBool(section string, option string) (bool, error)
    c.MustBool(section string, option string, defaultValue bool) bool
    
###注意：

    1. 每种类型都有两种方法去获取，直接GET当配置项不存在或发生错误时返回错误，MUST方法当配置项不存在或发生错误时返回默认值。
    2. 如果配置字段不包括在任何section中，也就是没写section，程序会自动写入 ***[default]*** 片段。
