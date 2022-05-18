package logkit

//兼容框架日志模式脚本

import (
	"fmt"
)

var DefaultLog = new(defaultLog)

type defaultLog struct {
}

func Default() *defaultLog {
	return DefaultLog
}

func (this *defaultLog) Print(v ...interface{}) {
	str := ""
	for _, i := range v {
		str += fmt.Sprintf("%v", i)
	}
	New(defaultTag).Info(str)
}

func (this *defaultLog) Printf(format string, v ...interface{}) {
	New(defaultTag).Info(fmt.Sprintf(format, v...))
}
func (this *defaultLog) Println(v ...interface{}) {
	str := ""
	for _, i := range v {
		str += fmt.Sprintf("%v", i)
	}
	New(defaultTag).Info(str)
}

func (this *defaultLog) Fatal(v ...interface{}) {
	str := ""
	for _, i := range v {
		str += fmt.Sprintf("%v", i)
	}
	New(defaultTag).Error(str)
}
func (this *defaultLog) Fatalf(format string, v ...interface{}) {
	New(defaultTag).Error(fmt.Sprintf(format, v...))
}
func (this *defaultLog) Fatalln(v ...interface{}) {
	str := ""
	for _, i := range v {
		str += fmt.Sprintf("%v", i)
	}
	New(defaultTag).Error(str)
}
func (this *defaultLog) Panic(v ...interface{}) {
	str := ""
	for _, i := range v {
		str += fmt.Sprintf("%v", i)
	}
	New(defaultTag).Error(str)
}
func (this *defaultLog) Panicf(format string, v ...interface{}) {
	New(defaultTag).Error(fmt.Sprintf(format, v...))
}
func (this *defaultLog) Panicln(v ...interface{}) {
	str := ""
	for _, i := range v {
		str += fmt.Sprintf("%v", i)
	}
	New(defaultTag).Error(str)
}
