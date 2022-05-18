package logkit

import (
	"log/syslog"
)

type XLoggerSyslog struct {
	logName   string
	logLevel  int
	logWriter *syslog.Writer
}

func (this *XLoggerSyslog) init(tag string, logName, logLevel string) {
	this.logName = logName
	this.logLevel = LoggerLevel[logLevel]
	this.logWriter = getWriter(this.logName + "/" + tag)
}

func getWriter(logName string) *syslog.Writer {
	writer, _ := syslog.New(syslog.LOG_INFO|syslog.LOG_LOCAL6, logName)
	return writer
}

func (this *XLoggerSyslog) Logger() *syslog.Writer {
	if this.logName == "" {
		panic("XLoggerSyslog log name missing")
	}
	if this.logWriter == nil {
		panic("XLoggerSyslog log writer missing")
	}
	return this.logWriter
}

func (this *XLoggerSyslog) Debug(str string, evts ...string) {
	tag, evt := caller(callerLevel, evts...)
	if this.logLevel <= LevelDebug {
		this.Logger().Info("tag[" + tag + "] " + "evt[" + evt + "] [debug] " + str)
	}
}
func (this *XLoggerSyslog) Info(str string, evts ...string) {
	tag, evt := caller(callerLevel, evts...)
	if this.logLevel <= LevelInfo {
		this.Logger().Info("tag[" + tag + "] " + "evt[" + evt + "] [info] " + str)
	}
}
func (this *XLoggerSyslog) Warn(str string, evts ...string) {
	tag, evt := caller(callerLevel, evts...)
	if this.logLevel <= LevelWarn {
		this.Logger().Info("tag[" + tag + "] " + "evt[" + evt + "] [warn] " + str)
	}
}
func (this *XLoggerSyslog) Error(str string, evts ...string) {
	tag, evt := caller(callerLevel, evts...)
	if this.logLevel <= LevelError {
		this.Logger().Info("tag[" + tag + "] " + "evt[" + evt + "] [error] " + str)
	}
}
