/*ng*
 * 日期转换操作
 */
package utils

import (
	"time"
)

func DateToTime(date string) int64 {
	format := "2006-01-02 15:04:05"
	nFormat := format[0:len(date)]

	loc, _ := time.LoadLocation("Local")
	stime, err := time.ParseInLocation(nFormat, date, loc)
	if err != nil {
		return 0
	}

	return stime.Unix()
}

func TimeToDate(timestamp int64) string {
	if timestamp <= 0 {
		return ""
	}
	tp := time.Unix(timestamp, 0)
	return tp.Format("2006-01-02")
}

func TimeToDatetime(timestamp int64) string {
	if timestamp <= 0 {
		return ""
	}
	tp := time.Unix(timestamp, 0)
	return tp.Format("2006-01-02 15:04:05")
}

//获取历史几天开始、结束时间戳
func GetLastDaySEtime(days int) (int64, int64) {
	dur := time.Duration(days*24) * time.Hour
	yesterday := time.Now().Add(dur).Format("2006-01-02")
	stime := DateToTime(yesterday + " 00:00:00")
	etime := DateToTime(yesterday + " 23:59:59")

	return stime, etime
}

//获取date 不带-字符串
func TimeToDt(timestamp int64) string {
	if timestamp <= 0 {
		return ""
	}
	tp := time.Unix(timestamp, 0)
	return tp.Format("20060102")
}
