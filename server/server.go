package main

import (
	"fmt"
	_ "image/gif"
	_ "image/jpeg"
	_ "image/png"
	"math/rand"
	"net/http"

	"image"

	"sync"
)

type server struct {
	image img
}

type img struct {
	sync.RWMutex
	frame image.Image
	count int
}

var message = []string{"right", "left"}

// curl http://localhost:8080/ -F "metadata=<plop.json" -F "file=@test.png" -vvv
func (s *server) ServeHTTP(w http.ResponseWriter, r *http.Request) {
	if r.Method == http.MethodPost {
		file, _, err := r.FormFile("file")
		defer file.Close()
		if err == nil {
			if m, _, err := image.Decode(file); err == nil {
				s.image.Lock()
				s.image.frame = m
				s.image.count++
				s.image.Unlock()

				fmt.Fprintf(w, "Commande envoyé")
				fmt.Fprintf(w, "Move %s", message[rand.Intn(2)])
			} else {
				fmt.Fprintf(w, "Mauvais format d'image")
				fmt.Println(err)
			}
		} else {
			fmt.Fprintf(w, "Pas d'image envoyé")
			fmt.Println(err)
		}
	} else {
		fmt.Fprintf(w, "Requête non-POST")
	}
}
