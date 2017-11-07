package main

import (
	"fmt"
	"html"
	"log"
	"net/http"
)

func main() {
	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "run, %q", html.EscapeString(r.URL.Path))
	})

	log.Fatal(http.ListenAndServe(":8082", nil))
}
