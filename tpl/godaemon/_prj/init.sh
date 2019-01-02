export PATH=.:/sbin:/usr/sbin:/usr/local/sbin:/usr/local/bin:/bin:/usr/bin:/usr/local/bin
USER=${USER/-/_}

NG=/data/x/tools/rigger-ng/rg
#% T.need_front : {

#%}
#% T.need_admin : {

#%}
if [ "${PRJ_ROOT}" == "" ];then
    PRJ_ROOT=$(cd `dirname $0`;cd ../; pwd)
fi
echo "Init the project ? "
echo "y(Yes) / n(No)? "d
read  yes
if ( test "$yes" = "y")
then
    echo "...........init data.......\n"
#% ( T.need_front == 'TRUE' ) and ( T.need_admin == 'FALSE' ) : {
    $NG conf,restart -e dev -s init,front,test
#% }
#% ( T.need_front == 'FALSE' ) and ( T.need_admin == 'TRUE' ) : {
    $NG conf,restart -e dev -s init,admin,test
#% }
#% ( T.need_front == 'TRUE' ) and ( T.need_admin == 'TRUE' ) : {
    $NG conf,restart -e dev -s init,front,admin,test
#% }
else
#% ( T.need_front == 'TRUE' ) and ( T.need_admin == 'FALSE' ) : {
    $NG conf,restart -e dev -s front,test
#% }
#% ( T.need_front == 'FALSE' ) and ( T.need_admin == 'TRUE' ) : {
    $NG conf,restart -e dev -s admin,test
#% }
#% ( T.need_front == 'TRUE' ) and ( T.need_admin == 'TRUE' ) : {
    $NG conf,restart -e dev -s front,admin,test
#% }
fi
