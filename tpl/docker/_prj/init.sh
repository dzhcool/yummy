export PATH=.:/sbin:/usr/sbin:/usr/local/sbin:/usr/local/bin:/bin:/usr/bin:/usr/local/bin
USER=${USER/-/_}

NG=/data/x/tools/rigger-ng/rg
if [ "${PRJ_ROOT}" == "" ];then
    PRJ_ROOT=$(cd `dirname $0`;cd ../; pwd)
fi
#% T.need_front : {

#%}
#% T.need_admin : {

#%}
echo "Init the project ? "
echo "y(Yes) / n(No)? "d
read  yes
if ( test "$yes" = "y")
then
    echo "...........init data.......\n"
    ${PRJ_ROOT}/run.sh conf dev
    ${PRJ_ROOT}/run.sh vendor
    ${PRJ_ROOT}/run.sh build
    ${PRJ_ROOT}/run.sh start
else
    ${PRJ_ROOT}/run.sh build
    ${PRJ_ROOT}/run.sh start
fi
