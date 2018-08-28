package model

import (
	"encoding/gob"
	"fmt"
	"log"
	"main/model/base"
	"main/modules/setting"
	"time"

	"github.com/jinzhu/gorm"
)

// 数据库链接
var db *gorm.DB

// Ping 测试数据库连接
func Ping() {
	db.DB().Ping()
}

func init() {
	var err error
	config := base.LoadConfigs("api")
	if db, err = base.NewEngine(config); err != nil {
		log.Fatalf("[orm] error: %v\n", err)
	}
	db.DB().SetMaxIdleConns(10)
	// 开启调试
	if setting.AppDebug {
		db.LogMode(true)
	}

	// 同步MySQL结构
	db.Set("gorm:table_options", "ENGINE=InnoDB DEFAULT CHARSET="+config.Charset).AutoMigrate()

	// 注册gob类型，for 缓存
	gob.Register(time.Time{})
}

func errorf(format string, a ...interface{}) error {
	if len(a) > 0 {
		return fmt.Errorf(format, a...)
	}
	return fmt.Errorf(format)
}

//数据库结构基础字段
type DBaseTime struct {
	Addtime int64  `json:"addtime" gorm:"type:int(10) unsigned NOT NULL DEFAULT '0';"`
	Addate  string `json:"addate" gorm:"-"`
	Uptime  int64  `json:"uptime" gorm:"type:int(10) unsigned NOT NULL DEFAULT '0';"`
	Update  string `json:"update" gorm:"-"`
}

//创建数据自动插入添加时间
func (p *DBaseTime) BeforeCreate(scope *gorm.Scope) (err error) {
	p.Addtime = time.Now().Unix()
	p.Uptime = p.Addtime
	return nil
}

//更新数据自动插入更新时间
func (p *DBaseTime) BeforeSave(scope *gorm.Scope) (err error) {
	uptime := time.Now().Unix()
	scope.SetColumn("uptime", uptime)
	return nil
}

func (p *DBaseTime) BeforeUpdate(scope *gorm.Scope) (err error) {
	uptime := time.Now().Unix()
	scope.SetColumn("uptime", uptime)
	return nil
}

type DBase struct {
	Id int `json:"id" gorm:"primary_key; type:int(11) unsigned NOT NULL AUTO_INCREMENT;"`
	DBaseTime
}

//模型基础配置
type model struct {
}
