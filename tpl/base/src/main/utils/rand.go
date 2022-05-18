package utils

import (
	"math/rand"
	"strconv"
	"time"
)

const (
	KC_RAND_KIND_NUM   = 0 // 纯数字
	KC_RAND_KIND_LOWER = 1 // 小写字母
	KC_RAND_KIND_UPPER = 2 // 大写字母
	KC_RAND_KIND_ALL   = 3 // 数字、大小写字母
)

// 随机字符串
func RandStr(size int, kind int) []byte {
	ikind, kinds, result := kind, [][]int{{10, 48}, {26, 97}, {26, 65}}, make([]byte, size)
	is_all := kind > 2 || kind < 0
	rand.Seed(time.Now().UnixNano())
	for i := 0; i < size; i++ {
		if is_all { // random ikind
			ikind = rand.Intn(3)
		}
		scope, base := kinds[ikind][0], kinds[ikind][1]
		result[i] = uint8(base + rand.Intn(scope))
	}
	return result
}

// RandFileName 随机生成一个文件名
func RandFileName() string {
	return strconv.FormatInt(time.Now().Unix(), 10) + string(RandStr(6, KC_RAND_KIND_NUM))
}

//生成随机数
func MtRand(m ...int) int {
	rand.Seed(time.Now().UnixNano())

	r := 0
	switch len(m) {
	case 0:
		r = rand.Intn(9)
	case 1:
		r = rand.Intn(m[0])
	case 2:
		r = rand.Intn(m[1])
		if r < m[0] {
			r = m[0]
		}
	default:
		r = rand.Intn(9)
	}
	return r
}

//获取起止范围内随机数
func RandRangeNumber(min, max int) int {
	r := rand.New(rand.NewSource(time.Now().UnixNano()))
	t := r.Intn(max)
	if t < min {
		t = RandRangeNumber(min, max)
	}
	return t
}
