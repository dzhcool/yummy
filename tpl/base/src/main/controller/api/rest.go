package api

import (
	"main/controller/base"
	pb "main/proto/api"

	"github.com/gin-gonic/gin"
)

/**
 * 该接口用来测试proto和json兼容模式，通过header传递 X-Transfer-Type 参数来决定用什么方式返回
 */

type restController struct {
}

var RestController = restController{}

func (p *restController) Get(c *gin.Context) {

	book := &pb.Book{
		Id:     1,
		Name:   "PHP高级程序设计",
		Author: "一抹尘",
		Year:   "2000",
	}

	res := &pb.BookResponse{
		Errno:  0,
		Errmsg: "",
		Book:   book,
	}

	base.Output(c, res)
}

func (p *restController) Lists(c *gin.Context) {

	books := make([]*pb.Book, 0, 10)

	for i := 0; i < 20; i += 2 {
		book := &pb.Book{
			Id:     int64(i),
			Name:   "Mysql从删库到跑路",
			Author: "一抹尘",
			Year:   "2000",
		}
		books = append(books, book)

		book2 := &pb.Book{
			Id:     int64(i + 1),
			Name:   "Golang从入门到放弃",
			Author: "一抹尘",
			Year:   "2019",
		}
		books = append(books, book2)
	}

	res := &pb.BooksResponse{
		Errno:  0,
		Errmsg: "",
		Book:   books,
	}

	base.Output(c, res)
}
