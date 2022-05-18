package logkit

import (
	"fmt"
	"main/modules/setting"
	"os"
	"runtime"
	"strconv"
	"strings"
)

type ILogger interface {
	init(tag string, logName, logLevel string)
	Debug(str string, evts ...string)
	Info(str string, evts ...string)
	Warn(str string, evts ...string)
	Error(str string, evts ...string)
}

var (
	loggers  map[string]ILogger //日志
	logFile  string             //log类型
	logName  string
	logLevel string
	logEnv   string
	logSys   string
	logUser  string
)

const callerLevel = 3
const defaultTag = "_main"

const (
	LevelDebug = iota
	LevelInfo
	LevelWarn
	LevelError
	LevelNone
)

var LoggerLevel = map[string]int{
	"debug": LevelDebug,
	"info":  LevelInfo,
	"warn":  LevelWarn,
	"error": LevelError,
	"none":  LevelNone,
}

func New(tags ...string) ILogger {
	return Log(tags...)
}

func Log(tags ...string) ILogger {
	var logger ILogger
	var exist bool

	tag := ""
	if len(tags) <= 0 {
		tag = "_default"
	} else {
		tag = tags[0]
	}

	if logger, exist = loggers[tag]; !exist {
		switch logFile {
		case "syslog":
			logger = new(XLoggerSyslog)
		case "stderr":
			logger = new(XLoggerStd)
		default:
			logger = new(XLoggerStd)
		}
		logger.init(tag, logName, logLevel)

		loggers[tag] = logger
	}
	return logger
}

//初始化基础信息
func init() {
	logName = setting.Config.MustString("logkit.logname", "logname")
	logFile = setting.Config.MustString("logkit.file", "stderr")
	logLevel = setting.Config.MustString("logkit.level", "error")
	logEnv = setting.Config.MustString("logkit.env", "dev")
	logSys = setting.Config.MustString("logkit.sys", "api")
	logUser = os.Getenv("USER")
	if logEnv == "dev" {
		logEnv = logUser
	}

	loggers = make(map[string]ILogger)
}

//获取调用者方法名
func caller(level int, evts ...string) (string, string) {
	evt := ""
	tag := logEnv + ",&" + logSys
	if len(evts) <= 0 {
		// pc, _, _, _ := runtime.Caller(level)
		_, file, line, _ := runtime.Caller(level)
		// evt = runtime.FuncForPC(pc).Name()
		callerfile := callerFile(file, line)
		evt = callerfile
	} else {
		evt = evts[0]
	}
	return tag, evt
}

//截取调用者文件名前缀，截取到src开头
func callerFile(file string, line int) string {
	idx := strings.LastIndex(file, "src")

	if idx > 0 {
		file = file[idx:]
	}
	return file + ":" + strconv.Itoa(line)
}

//外部调用方法
func Debug(str string, evts ...string) {
	New(defaultTag).Debug(str, evts...)
}

func Info(str string, evts ...string) {
	New(defaultTag).Info(str, evts...)
}

func Warn(str string, evts ...string) {
	New(defaultTag).Warn(str, evts...)
}

func Error(str string, evts ...string) {
	New(defaultTag).Error(str, evts...)
}

//外部调用方法，带格format式化
func Debugf(format string, params ...interface{}) {
	New(defaultTag).Debug(fmt.Sprintf(format, params...))
}

func Infof(format string, params ...interface{}) {
	New(defaultTag).Info(fmt.Sprintf(format, params...))
}

func Warnf(format string, params ...interface{}) {
	New(defaultTag).Warn(fmt.Sprintf(format, params...))
}

func Errorf(format string, params ...interface{}) {
	New(defaultTag).Error(fmt.Sprintf(format, params...))
}

//兼容老的写法,后期废弃
func Println(params ...interface{}) {
	str := ""
	for _, i := range params {
		str += fmt.Sprintf("%v", i)
	}
	New(defaultTag).Error(str)
}

func Printf(format string, params ...interface{}) {
	New(defaultTag).Error(fmt.Sprintf(format, params...))
}

func Fatal(params ...interface{}) {
	str := ""
	for _, i := range params {
		str += fmt.Sprintf("%v", i)
	}
	New(defaultTag).Error(str)
}

func Fatalf(format string, params ...interface{}) {
	New(defaultTag).Error(fmt.Sprintf(format, params...))
}
