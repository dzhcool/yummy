#!/bin/sh
# 项目启动脚本
# @date 2017-10-23
# @version 1.0

USER=${USER/-/_}
PRJ_ROOT=$(cd `dirname $0`; pwd)
PRJ_NAME=`basename ${PRJ_ROOT}`

ENV=$2
if [ "${ENV}" == "" ];then
    ENV="dev"
fi
BIN_NAME="%{PROJECT}" # 生成二进制文件文件名，也是docker镜像名称
HUB="%{HUB}"  # 镜像hub地址
PACKAGE="main" # main包名称
BIN=${PRJ_ROOT}/bin/${BIN_NAME} # 生成二进制文件路径
BIN_DOCKER="sudo docker " # docker二进制文件路径
PRJ_NAME=`basename ${PACKAGE}`
DOCKER_WORKER="${PRJ_ROOT}/_docker/"

echo "-------------------- ${PRJ_NAME} --------------------"

export PATH=.:/sbin:/usr/sbin:/usr/local/sbin:/usr/local/bin:/bin:/usr/bin:/usr/local/bin
export GOPATH=${PRJ_ROOT}
export BAA_ROOT=${PRJ_ROOT}
echo "GOPATH:${GOPATH}"

# 操作定义
function run_conf(){
    cp -rf ${PRJ_ROOT}/version.txt ${PRJ_ROOT}/conf/
}

function run_vendor(){
    cd ${PRJ_ROOT}/src/${PACKAGE}
    if [ ! -d vendor/ ];then
        govendor init
    fi
    govendor add +external
}

function run_build(){
    govendor install ${PRJ_NAME}
    if [ -f "${PRJ_ROOT}/bin/${PACKAGE}" ];then
        mv ${PRJ_ROOT}/bin/${PACKAGE} ${BIN}
    else
        exit 1
    fi
}

function run_docker_build(){
    run_conf # cp配置文件
    CGO_ENABLED=0 GOOS=linux govendor build -a -installsuffix cgo -o ${PRJ_NAME} ${PRJ_NAME}

    if [ ! -f "${PACKAGE}" ];then
        exit 1
    fi
    mv ${PACKAGE} conf/${BIN_NAME}

    docker_kill ${BIN_NAME}
    docker_rmi ${BIN_NAME}
    ${BIN_DOCKER} build -t ${BIN_NAME}:${ENV} conf/
}

function run_start(){
    if ! test -e ${BIN} ;then
        echo "${BIN} not exists"
        exit 1
    fi
    num=`ps aux|grep "${BIN}"|grep -v "grep" -c`
    if [ $num -ge 1 ];then
        run_stop
    fi
    nohup ${BIN} > /tmp/${BIN_NAME}_${PRJ_NAME}.log 2>&1 &
}

function run_restart(){
    run_stop
    run_start
}

function run_stop(){
    ID=`ps aux|grep "${BIN}"|grep -v "grep"|awk -F ' ' '{print $2}'`
    for id in $ID
    do
        kill -9 $id
    done
}

# 删除docker镜像
function docker_rmi(){
    name=$1
    imageids=`${BIN_DOCKER} images|grep "${name}"|grep "${ENV}"|grep -v "grep"|awk -F ' ' '{print $3}'`
    for imageid in ${imageids}
    do
        ${BIN_DOCKER} rmi -f ${imageid}
    done
}

# 删除docker container
function docker_kill(){
    name=$1
    containerids=`${BIN_DOCKER} ps |grep "${name}"|grep "${ENV}"|grep -v "grep"|awk -F ' ' '{print $1}'`
    for containerid in ${containerids}
    do
        ${BIN_DOCKER} kill  ${containerid}
    done
}

# 推送docker镜像
function run_docker_push(){
    ${BIN_DOCKER} tag ${BIN_NAME}:$ENV ${HUB}/${BIN_NAME}:$ENV
    ${BIN_DOCKER} push ${HUB}/${BIN_NAME}:$ENV
}

case $1 in
    conf)
        echo " Exec Conf "
        run_conf
        exit;
        ;;
    vendor)
        echo " Exec vendor "
        run_vendor
        exit;
        ;;
    build)
        echo " Exec build "
        run_build
        exit;
        ;;
    dockerbuild)
        echo " Exec docker build "
        run_docker_build
        exit;
        ;;
    dockerpush)
        echo " Exec docker push "
        run_docker_push
        exit;
        ;;
    start)
        echo " Exec start "
        run_start
        exit;
        ;;
    restart)
        echo " Exec start "
        run_restart
        exit;
        ;;
    stop)
        echo " Exec stop "
        run_stop
        exit;
        ;;
    *) #default
        echo " Run as: ./run.sh conf [dev beta online]|vendor|start|stop"
        exit;
        ;;
esac

echo "*****************End***********"

