// Code generated by protoc-gen-go. DO NOT EDIT.
// source: api/book.proto

package api

import (
	fmt "fmt"
	proto "github.com/golang/protobuf/proto"
	math "math"
)

// Reference imports to suppress errors if they are not otherwise used.
var _ = proto.Marshal
var _ = fmt.Errorf
var _ = math.Inf

// This is a compile-time assertion to ensure that this generated file
// is compatible with the proto package it is being compiled against.
// A compilation error at this line likely means your copy of the
// proto package needs to be updated.
const _ = proto.ProtoPackageIsVersion3 // please upgrade the proto package

type Book struct {
	Id                   int64    `protobuf:"varint,1,opt,name=id,proto3" json:"id"`
	Name                 string   `protobuf:"bytes,2,opt,name=name,proto3" json:"name"`
	Author               string   `protobuf:"bytes,3,opt,name=author,proto3" json:"author"`
	Year                 string   `protobuf:"bytes,4,opt,name=year,proto3" json:"year"`
	XXX_NoUnkeyedLiteral struct{} `json:"-"`
	XXX_unrecognized     []byte   `json:"-"`
	XXX_sizecache        int32    `json:"-"`
}

func (m *Book) Reset()         { *m = Book{} }
func (m *Book) String() string { return proto.CompactTextString(m) }
func (*Book) ProtoMessage()    {}
func (*Book) Descriptor() ([]byte, []int) {
	return fileDescriptor_de6d8158b3ebbb9c, []int{0}
}

func (m *Book) XXX_Unmarshal(b []byte) error {
	return xxx_messageInfo_Book.Unmarshal(m, b)
}
func (m *Book) XXX_Marshal(b []byte, deterministic bool) ([]byte, error) {
	return xxx_messageInfo_Book.Marshal(b, m, deterministic)
}
func (m *Book) XXX_Merge(src proto.Message) {
	xxx_messageInfo_Book.Merge(m, src)
}
func (m *Book) XXX_Size() int {
	return xxx_messageInfo_Book.Size(m)
}
func (m *Book) XXX_DiscardUnknown() {
	xxx_messageInfo_Book.DiscardUnknown(m)
}

var xxx_messageInfo_Book proto.InternalMessageInfo

func (m *Book) GetId() int64 {
	if m != nil {
		return m.Id
	}
	return 0
}

func (m *Book) GetName() string {
	if m != nil {
		return m.Name
	}
	return ""
}

func (m *Book) GetAuthor() string {
	if m != nil {
		return m.Author
	}
	return ""
}

func (m *Book) GetYear() string {
	if m != nil {
		return m.Year
	}
	return ""
}

// book request
type BookRequest struct {
	// 基础分页参数，预留
	Page     int32 `protobuf:"varint,1,opt,name=page,proto3" json:"page"`
	Pagesize int32 `protobuf:"varint,2,opt,name=pagesize,proto3" json:"pagesize"`
	// 业务参数
	Id                   int64    `protobuf:"varint,3,opt,name=id,proto3" json:"id"`
	Name                 string   `protobuf:"bytes,4,opt,name=name,proto3" json:"name"`
	XXX_NoUnkeyedLiteral struct{} `json:"-"`
	XXX_unrecognized     []byte   `json:"-"`
	XXX_sizecache        int32    `json:"-"`
}

func (m *BookRequest) Reset()         { *m = BookRequest{} }
func (m *BookRequest) String() string { return proto.CompactTextString(m) }
func (*BookRequest) ProtoMessage()    {}
func (*BookRequest) Descriptor() ([]byte, []int) {
	return fileDescriptor_de6d8158b3ebbb9c, []int{1}
}

func (m *BookRequest) XXX_Unmarshal(b []byte) error {
	return xxx_messageInfo_BookRequest.Unmarshal(m, b)
}
func (m *BookRequest) XXX_Marshal(b []byte, deterministic bool) ([]byte, error) {
	return xxx_messageInfo_BookRequest.Marshal(b, m, deterministic)
}
func (m *BookRequest) XXX_Merge(src proto.Message) {
	xxx_messageInfo_BookRequest.Merge(m, src)
}
func (m *BookRequest) XXX_Size() int {
	return xxx_messageInfo_BookRequest.Size(m)
}
func (m *BookRequest) XXX_DiscardUnknown() {
	xxx_messageInfo_BookRequest.DiscardUnknown(m)
}

var xxx_messageInfo_BookRequest proto.InternalMessageInfo

func (m *BookRequest) GetPage() int32 {
	if m != nil {
		return m.Page
	}
	return 0
}

func (m *BookRequest) GetPagesize() int32 {
	if m != nil {
		return m.Pagesize
	}
	return 0
}

func (m *BookRequest) GetId() int64 {
	if m != nil {
		return m.Id
	}
	return 0
}

func (m *BookRequest) GetName() string {
	if m != nil {
		return m.Name
	}
	return ""
}

// book response
type BookResponse struct {
	// 接口状态参数，预留
	Errno  ResponseStatus `protobuf:"varint,1,opt,name=errno,proto3,enum=v4api.ResponseStatus" json:"errno"`
	Errmsg string         `protobuf:"bytes,2,opt,name=errmsg,proto3" json:"errmsg"`
	// 业务参数
	Book                 *Book    `protobuf:"bytes,3,opt,name=book,proto3" json:"book"`
	XXX_NoUnkeyedLiteral struct{} `json:"-"`
	XXX_unrecognized     []byte   `json:"-"`
	XXX_sizecache        int32    `json:"-"`
}

func (m *BookResponse) Reset()         { *m = BookResponse{} }
func (m *BookResponse) String() string { return proto.CompactTextString(m) }
func (*BookResponse) ProtoMessage()    {}
func (*BookResponse) Descriptor() ([]byte, []int) {
	return fileDescriptor_de6d8158b3ebbb9c, []int{2}
}

func (m *BookResponse) XXX_Unmarshal(b []byte) error {
	return xxx_messageInfo_BookResponse.Unmarshal(m, b)
}
func (m *BookResponse) XXX_Marshal(b []byte, deterministic bool) ([]byte, error) {
	return xxx_messageInfo_BookResponse.Marshal(b, m, deterministic)
}
func (m *BookResponse) XXX_Merge(src proto.Message) {
	xxx_messageInfo_BookResponse.Merge(m, src)
}
func (m *BookResponse) XXX_Size() int {
	return xxx_messageInfo_BookResponse.Size(m)
}
func (m *BookResponse) XXX_DiscardUnknown() {
	xxx_messageInfo_BookResponse.DiscardUnknown(m)
}

var xxx_messageInfo_BookResponse proto.InternalMessageInfo

func (m *BookResponse) GetErrno() ResponseStatus {
	if m != nil {
		return m.Errno
	}
	return ResponseStatus_OK
}

func (m *BookResponse) GetErrmsg() string {
	if m != nil {
		return m.Errmsg
	}
	return ""
}

func (m *BookResponse) GetBook() *Book {
	if m != nil {
		return m.Book
	}
	return nil
}

type BooksResponse struct {
	// 接口状态参数，预留
	// int32 errno = 1;
	Errno  ResponseStatus `protobuf:"varint,1,opt,name=errno,proto3,enum=v4api.ResponseStatus" json:"errno"`
	Errmsg string         `protobuf:"bytes,2,opt,name=errmsg,proto3" json:"errmsg"`
	// 业务参数
	Book                 []*Book  `protobuf:"bytes,3,rep,name=book,proto3" json:"book"`
	Count                int64    `protobuf:"varint,4,opt,name=count,proto3" json:"count"`
	XXX_NoUnkeyedLiteral struct{} `json:"-"`
	XXX_unrecognized     []byte   `json:"-"`
	XXX_sizecache        int32    `json:"-"`
}

func (m *BooksResponse) Reset()         { *m = BooksResponse{} }
func (m *BooksResponse) String() string { return proto.CompactTextString(m) }
func (*BooksResponse) ProtoMessage()    {}
func (*BooksResponse) Descriptor() ([]byte, []int) {
	return fileDescriptor_de6d8158b3ebbb9c, []int{3}
}

func (m *BooksResponse) XXX_Unmarshal(b []byte) error {
	return xxx_messageInfo_BooksResponse.Unmarshal(m, b)
}
func (m *BooksResponse) XXX_Marshal(b []byte, deterministic bool) ([]byte, error) {
	return xxx_messageInfo_BooksResponse.Marshal(b, m, deterministic)
}
func (m *BooksResponse) XXX_Merge(src proto.Message) {
	xxx_messageInfo_BooksResponse.Merge(m, src)
}
func (m *BooksResponse) XXX_Size() int {
	return xxx_messageInfo_BooksResponse.Size(m)
}
func (m *BooksResponse) XXX_DiscardUnknown() {
	xxx_messageInfo_BooksResponse.DiscardUnknown(m)
}

var xxx_messageInfo_BooksResponse proto.InternalMessageInfo

func (m *BooksResponse) GetErrno() ResponseStatus {
	if m != nil {
		return m.Errno
	}
	return ResponseStatus_OK
}

func (m *BooksResponse) GetErrmsg() string {
	if m != nil {
		return m.Errmsg
	}
	return ""
}

func (m *BooksResponse) GetBook() []*Book {
	if m != nil {
		return m.Book
	}
	return nil
}

func (m *BooksResponse) GetCount() int64 {
	if m != nil {
		return m.Count
	}
	return 0
}

func init() {
	proto.RegisterType((*Book)(nil), "v4api.Book")
	proto.RegisterType((*BookRequest)(nil), "v4api.BookRequest")
	proto.RegisterType((*BookResponse)(nil), "v4api.BookResponse")
	proto.RegisterType((*BooksResponse)(nil), "v4api.BooksResponse")
}

func init() { proto.RegisterFile("v4api/book.proto", fileDescriptor_de6d8158b3ebbb9c) }

var fileDescriptor_de6d8158b3ebbb9c = []byte{
	// 264 bytes of a gzipped FileDescriptorProto
	0x1f, 0x8b, 0x08, 0x00, 0x00, 0x00, 0x00, 0x00, 0x02, 0xff, 0xb4, 0x91, 0xc1, 0x4a, 0xc4, 0x30,
	0x10, 0x86, 0x69, 0x93, 0x2c, 0x3a, 0xd5, 0x45, 0x82, 0x4a, 0xd9, 0x8b, 0x4b, 0x4f, 0x0b, 0x42,
	0x85, 0xd5, 0x27, 0xf0, 0x11, 0x22, 0x78, 0xcf, 0xba, 0xc3, 0x5a, 0x96, 0xed, 0xd4, 0x24, 0x15,
	0xf4, 0x15, 0x7c, 0x69, 0xc9, 0x24, 0xd5, 0x83, 0x5e, 0xf7, 0xd4, 0xf9, 0xf3, 0xff, 0xcc, 0x37,
	0x33, 0x85, 0x8b, 0xf7, 0x07, 0x3b, 0x74, 0x77, 0x1b, 0xa2, 0x7d, 0x3b, 0x38, 0x0a, 0xa4, 0x15,
	0xbf, 0x2c, 0x26, 0xc3, 0x7a, 0x4c, 0x46, 0xf3, 0x0c, 0xf2, 0x91, 0x68, 0xaf, 0xe7, 0x50, 0x76,
	0xdb, 0xba, 0x58, 0x16, 0x2b, 0x61, 0xca, 0x6e, 0xab, 0x35, 0xc8, 0xde, 0x1e, 0xb0, 0x2e, 0x97,
	0xc5, 0xea, 0xd4, 0x70, 0xad, 0xaf, 0x61, 0x66, 0xc7, 0xf0, 0x4a, 0xae, 0x16, 0xfc, 0x9a, 0x55,
	0xcc, 0x7e, 0xa0, 0x75, 0xb5, 0x4c, 0xd9, 0x58, 0x37, 0x16, 0xaa, 0xd8, 0xd7, 0xe0, 0xdb, 0x88,
	0x3e, 0xc4, 0xc8, 0x60, 0x77, 0xc8, 0x00, 0x65, 0xb8, 0xd6, 0x0b, 0x38, 0x89, 0x5f, 0xdf, 0x7d,
	0x26, 0x8c, 0x32, 0x3f, 0x3a, 0x8f, 0x23, 0xfe, 0x8c, 0x23, 0x7f, 0xc7, 0x69, 0x02, 0x9c, 0x25,
	0x84, 0x1f, 0xa8, 0xf7, 0xa8, 0x6f, 0x41, 0xa1, 0x73, 0x3d, 0x31, 0x64, 0xbe, 0xbe, 0x6a, 0x79,
	0xd9, 0x76, 0xf2, 0x9f, 0x82, 0x0d, 0xa3, 0x37, 0x29, 0x13, 0x77, 0x41, 0xe7, 0x0e, 0x7e, 0x97,
	0x37, 0xcc, 0x4a, 0xdf, 0x80, 0x8c, 0x67, 0x63, 0x74, 0xb5, 0xae, 0x72, 0x0f, 0xe6, 0xb0, 0xd1,
	0x7c, 0x15, 0x70, 0x1e, 0xa5, 0x3f, 0x16, 0x57, 0xfc, 0xcb, 0xd5, 0x97, 0xa0, 0x5e, 0x68, 0xec,
	0x03, 0x9f, 0x40, 0x98, 0x24, 0x36, 0x33, 0xfe, 0x8b, 0xf7, 0xdf, 0x01, 0x00, 0x00, 0xff, 0xff,
	0xaa, 0xdb, 0x5b, 0x06, 0xf2, 0x01, 0x00, 0x00,
}
