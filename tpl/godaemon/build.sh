#!/bin/bash

SYS_PATH="${SYS_PATH}"

TARGET_CONF_DIR="${SYS_PATH}/conf/"

GOSOCK_DIR="/var/run/rgapp-${USER}-${PRJ_KEY}-${APP_SYS}/"

BIN="${SYS_PATH}/bin/${PRJ_NAME}"

function clean()
{
    rm -rf $SYS_PATH/pkg/ $BIN $TARGET_CONF
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

function build()
{
    cd ${SYS_PATH}

    govendor install main
    # go install main

    mv "${SYS_PATH}/bin/main" $BIN

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


echo "***********************Config SYS-${SYS_NAME}. Please Waiting*****************************"

# check
# clean
# conf
build
gotest

echo "***********************Config SYS-${SYS_NAME}. END****************************************"
exit 0
