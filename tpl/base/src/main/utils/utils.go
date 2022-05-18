package utils

import (
	"crypto/md5"
	"encoding/hex"
	"io"
	"os"
)

func Getenv(key string, args ...string) string {
	val := os.Getenv(key)
	if len(val) <= 0 || len(args) >= 2 {
		val = args[0]
	}
	return val
}

// MD5 checksum for str
func MD5(str string) string {
	hexStr := md5.Sum([]byte(str))
	return hex.EncodeToString(hexStr[:])
}

// MD5File checksum for file path
func MD5File(filepath string) string {
	f, err := os.Open(filepath)
	if err != nil {
		return ""
	}

	defer f.Close()
	md5hash := md5.New()
	if _, err := io.Copy(md5hash, f); err != nil {
		return ""
	}

	hexStr := md5hash.Sum(nil)
	return hex.EncodeToString(hexStr[:])
}
