#!/bin/bash

# export GOPROXY="https://athens.azurefd.net"  # go下载代理

SYS_PATH="${PRJ_ROOT}/src/${SYS_ALIAS}"

TARGET_CONF_DIR="${SYS_PATH}/conf/"

GOSOCK_DIR="/var/run/rgapp-${USER}-${PRJ_KEY}-${APP_SYS}/"

BIN="${PRJ_ROOT}/bin/${SYS_ALIAS}"

function clean()
{
    rm -rf ${PRJ_ROOT}/pkg/ $BIN $TARGET_CONF
}

function check()
{
    go version
    if ! test -e $TARGET_CONF_DIR ; then
        mkdir $TARGET_CONF_DIR
    fi

    if ! test -e $GOSOCK_DIR ; then
        mkdir $GOSOCK_DIR
    fi

    if ! test -e $SOURCE_CONF ; then
        echo "$SOURCE_CONF not exists, setup failed..."
        echo "***********************Config SYS-${APP_SYS}. Failed****************************************"
        exit -1
    fi
}

function conf()
{
    ln -s $SOURCE_CONF $TARGET_CONF

    if ! test -e $TARGET_CONF; then
        echo "Conf File [N]"
        exit -1
    else
        echo "Conf File [Y]"
    fi
}

function initMod()
{
    if ! test -e ${SYS_PATH}/go.mod; then
        cd ${SYS_PATH}
        echo "go mod init ${SYS_ALIAS}"
        go mod init ${SYS_ALIAS}
    fi
}

function build()
{
    cd ${SYS_PATH}

    # govendor install main
    go build .

    mv "${SYS_PATH}/${SYS_ALIAS}" $BIN

    if ! test -e $BIN ; then
        echo "Complie   [N]"
        exit -1
    else
        echo "Complie   [Y]"
    fi
}

function gotest()
{
    echo "Testing   [...]"
    # go test -v main/dot
}


echo "***********************Config SYS-${SYS_ALIAS}. Please Waiting*****************************"

# check
# clean
# conf
initMod
build
gotest

echo "***********************Config SYS-${SYS_ALIAS}. END****************************************"
exit 0
