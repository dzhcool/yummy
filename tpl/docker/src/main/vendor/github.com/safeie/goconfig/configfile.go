package goconfig

import (
	"bufio"
	"errors"
	"fmt"
	"io"
	"os"
	"regexp"
	"strconv"
	"strings"
)

// ConfigFile config handler
type ConfigFile struct {
	data map[string]map[string]string
}

var (
	// DefaultSection if item not in any section, store in default section
	DefaultSection = "default"

	// BoolStrings parse to bool values
	BoolStrings = map[string]bool{
		"0":     false,
		"1":     true,
		"false": false,
		"true":  true,
		"n":     false,
		"y":     true,
		"no":    false,
		"yes":   true,
		"off":   false,
		"on":    true,
	}
)

// AddSection add a new section
func (c *ConfigFile) AddSection(section string) bool {
	section = strings.ToLower(section)
	if _, ok := c.data[section]; ok {
		return false // section exists
	}
	c.data[section] = make(map[string]string)
	return true
}

// RemoveSection remove an exists section
func (c *ConfigFile) RemoveSection(section string) bool {
	section = strings.ToLower(section)
	if _, ok := c.data[section]; ok {
		delete(c.data, section)
	}
	return true
}

// AddOption add a new option with section
func (c *ConfigFile) AddOption(section, option, value string) bool {
	c.AddSection(section)
	section = strings.ToLower(section)
	option = strings.ToLower(option)
	if _, ok := c.data[section][option]; ok {
		//return false	// option exists
		//we need update vale, so do not return
	}
	c.data[section][option] = value
	return true
}

// RemoveOption remove an exists option with section
func (c *ConfigFile) RemoveOption(section, option string) bool {
	section = strings.ToLower(section)
	option = strings.ToLower(option)
	if _, ok := c.data[section]; !ok {
		return true
	}
	if _, ok := c.data[section][option]; ok {
		delete(c.data[section], option)
	}
	return true
}

// GetRawString get the raw option values
func (c *ConfigFile) GetRawString(section, option string) (string, error) {
	section = strings.ToLower(section)
	option = strings.ToLower(option)

	if _, ok := c.data[section]; ok {
		if value, ok := c.data[section][option]; ok {
			return value, nil
		}
		return "", fmt.Errorf("Option not found: %s", option)
	}
	return "", fmt.Errorf("Section not found: %s", section)
}

// GetString format value and return string
func (c *ConfigFile) GetString(section, option string) (string, error) {
	value, err := c.GetRawString(section, option)
	if err != nil {
		return "", err
	}
	return value, nil
}

// GetInt format value and return int
func (c *ConfigFile) GetInt(section, option string) (int, error) {
	value, err := c.GetInt64(section, option)
	if err != nil {
		return 0, err
	}
	return int(value), nil
}

// GetInt64 format value and return int64
func (c *ConfigFile) GetInt64(section, option string) (int64, error) {
	value, err := c.GetRawString(section, option)
	if err != nil {
		return 0, err
	}
	iv, err := strconv.ParseInt(value, 10, 64)
	if err != nil {
		return 0, err
	}
	return iv, nil
}

// GetFloat format value and return float64
func (c *ConfigFile) GetFloat(section, option string) (float64, error) {
	value, err := c.GetRawString(section, option)
	if err != nil {
		return float64(0), err
	}
	fv, err := strconv.ParseFloat(value, 64)
	if err != nil {
		return float64(0), err
	}
	return fv, nil
}

// GetBool format value and return bool
func (c *ConfigFile) GetBool(section, option string) (bool, error) {
	value, err := c.GetRawString(section, option)
	if err != nil {
		return false, err
	}
	bv, ok := BoolStrings[strings.ToLower(value)]
	if ok == false {
		return false, fmt.Errorf("Cound not parse bool value: %s", value)
	}
	return bv, nil
}

// MustString format value and return string or default value
func (c *ConfigFile) MustString(section, option, value string) string {
	val, err := c.GetString(section, option)
	if err != nil || val == "" {
		return value
	}
	return val
}

// MustInt format value and return int or default value
func (c *ConfigFile) MustInt(section, option string, value int) int {
	val, err := c.GetInt(section, option)
	if err != nil || val == 0 {
		return value
	}
	return val
}

// MustInt64 format value and return int64 or default value
func (c *ConfigFile) MustInt64(section, option string, value int64) int64 {
	val, err := c.GetInt64(section, option)
	if err != nil || val == 0 {
		return value
	}
	return val
}

// MustFloat format value and return float64 or default value
func (c *ConfigFile) MustFloat(section, option string, value float64) float64 {
	val, err := c.GetFloat(section, option)
	if err != nil || val == 0.0 {
		return value
	}
	return val
}

// MustBool format value and return bool or default value
func (c *ConfigFile) MustBool(section, option string, value bool) bool {
	val, err := c.GetBool(section, option)
	if err != nil {
		return value
	}
	return val
}

// NewConfigFile init a new ConfigFile
func NewConfigFile() *ConfigFile {
	c := new(ConfigFile)
	c.data = make(map[string]map[string]string)
	c.AddSection(DefaultSection) // deafult section always exists
	return c
}

// find delimiter first occur position
func firstIndex(l string, delimiter []byte) int {
	for i := 0; i < len(delimiter); i++ {
		if j := strings.Index(l, string(delimiter[i])); j != -1 {
			return j
		}
	}
	return -1
}

// strip comment in value
func stripComments(l string) string {
	for _, c := range []string{" ;", "\t;", " #", "\t#"} {
		if i := strings.Index(l, c); i != -1 {
			l = l[0:i]
		}
	}
	return l
}

func (c *ConfigFile) read(buf *bufio.Reader) error {
	var section, option string
	section = DefaultSection
	for {
		l, err := buf.ReadString('\n') // parse line-by-line
		if l == "" && err != nil {
			if err == io.EOF {
				break
			}
			return err
		}
		l = strings.TrimSpace(l)
		//switch written for readability
		switch {
		case len(l) == 0: //empty line
			continue
		case l[0] == '#': //comment
			continue
		case l[0] == ';': //comment
			continue
		case len(l) >= 3 && strings.ToLower(l[0:3]) == "rem": // comment for windows
			continue
		case l[0] == '[' && l[len(l)-1] == ']': // new section
			option = "" // reset multi-line value
			section = strings.TrimSpace(l[1 : len(l)-1])
			c.AddSection(section)
		case section == "": // not new section and no sectiondefined so far
			return errors.New("Section not found: must start with section")
		default: // other alternatives
			i := firstIndex(l, []byte{'=', ':'})
			switch {
			case i > 0:
				option = strings.TrimSpace(l[0:i])
				value := strings.TrimSpace(stripComments(l[i+1:]))
				value = strings.Trim(value, "\"")
				value = strings.Trim(value, "'")
				value = strings.Trim(value, "`")
				c.AddOption(section, option, value)
			case section != "" && option != "":
				// continuation of multi-line value
				prev, _ := c.GetRawString(section, option)
				value := strings.TrimSpace(stripComments(l))
				c.AddOption(section, option, prev+"\n"+value)
			default:
				return fmt.Errorf("Cound not parse line: %s", l)
			}

		}
	}
	return nil
}

// parseEnv parse system ENV in values
func (c *ConfigFile) parseEnv() {
	for section := range c.data {
		for option := range c.data[section] {
			c.data[section][option] = parseEnv(c.data[section][option])
		}
	}
}

// parseVariables parse variables in values
func (c *ConfigFile) parseVariables() {
	var hasVariable bool
	var varSection, varOption string
	re, err := regexp.Compile("{{(?U:.+)}}")
	if err != nil {
		return
	}
	for {
		hasVariable = false
		for section := range c.data {
			for option := range c.data[section] {
				c.data[section][option] = re.ReplaceAllStringFunc(c.data[section][option],
					func(s string) string {
						hasVariable = true
						ss := strings.TrimSpace(s[2 : len(s)-2])
						pos := strings.Index(ss, ".")
						if pos < 1 {
							varSection = section
						} else {
							varSection = ss[:pos]
						}
						varOption = ss[pos+1:]
						val, err := c.GetString(varSection, varOption)
						if err != nil {
							return "[VARIABLES_PARSE_ERROR:" + err.Error() + "]"
						}
						return val
					})
			}
		}
		if !hasVariable {
			break
		}
	}
}

// ReadConfigFile create a configFile handler from a config file and returns
func ReadConfigFile(f string) (*ConfigFile, error) {
	file, err := os.Open(f)
	if err != nil {
		return nil, err
	}
	c := NewConfigFile()
	if err = c.read(bufio.NewReader(file)); err != nil {
		return nil, err
	}
	if err = file.Close(); err != nil {
		return nil, err
	}
	c.parseEnv()
	c.parseVariables()
	return c, nil
}

// parseEnv parse system ENV in values
func parseEnv(s string) string {
	re, err := regexp.Compile("{{(?U:.+)}}")
	if err != nil {
		return "[ENV_PARSE_ERROR:" + err.Error() + "]"
	}
	return re.ReplaceAllStringFunc(s, func(s string) string {
		ss := strings.TrimSpace(s[2 : len(s)-2])
		if len(ss) > 4 && ss[:4] == "ENV:" {
			return os.Getenv(ss[4:])
		}
		return s
	})
}
