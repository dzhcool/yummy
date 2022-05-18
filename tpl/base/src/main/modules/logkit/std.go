package logkit

import (
	"fmt"
	"io"
	"os"
	"time"
)

type XLoggerStd struct {
	logName   string
	logLevel  int
	logWriter io.Writer
}

func (this *XLoggerStd) init(tag string, logName, logLevel string) {
	this.logName = logName
	this.logLevel = LoggerLevel[logLevel]
	this.logWriter = os.Stderr
}

func (this *XLoggerStd) now() string {
	return time.Now().Format("Mon Jan 2 15:04:05 -0700 MST 2006")
}

func (this *XLoggerStd) Debug(str string, evts ...string) {
	tag, evt := caller(callerLevel, evts...)
	logtime := this.now()
	if this.logLevel <= LevelDebug {
		fmt.Fprint(this.logWriter, logtime+" tag["+tag+"] "+"evt["+evt+"] [debug] "+str+"\n")
	}
}

func (this *XLoggerStd) Info(str string, evts ...string) {
	tag, evt := caller(callerLevel, evts...)
	logtime := this.now()
	if this.logLevel <= LevelInfo {
		fmt.Fprint(this.logWriter, logtime+" tag["+tag+"] "+"evt["+evt+"] [info] "+str+"\n")
	}
}

func (this *XLoggerStd) Warn(str string, evts ...string) {
	tag, evt := caller(callerLevel, evts...)
	logtime := this.now()
	if this.logLevel <= LevelWarn {
		fmt.Fprint(this.logWriter, logtime+" tag["+tag+"] "+"evt["+evt+"] [warn] "+str+"\n")
	}
}

func (this *XLoggerStd) Error(str string, evts ...string) {
	tag, evt := caller(callerLevel, evts...)
	logtime := this.now()
	if this.logLevel <= LevelError {
		fmt.Fprint(this.logWriter, logtime+" tag["+tag+"] "+"evt["+evt+"] [error] "+str+"\n")
	}
}
