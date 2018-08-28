#!/bin/sh
# 依赖扩展获取
# @date 2017-10-23

PRJ_ROOT=$(cd `dirname $0`; pwd)
export GOPATH=${PRJ_ROOT}
echo "GOPATH:${GOPATH}"

# go get xxx
go get github.com/gin-gonic/gin
go get github.com/dgrijalva/jwt-go
go get github.com/go-sql-driver/mysql
go get github.com/jinzhu/gorm
go get golang.org/x/sys/unix


exit 0
