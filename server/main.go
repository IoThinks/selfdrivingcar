package main

import (
	"fmt"
	"image"
	"net/http"

	"time"

	"github.com/faiface/pixel"
	"github.com/faiface/pixel/pixelgl"
)

func main() {
	pixelgl.Run(run)
}

const (
	width  = 640
	height = 480
)

func run() {
	win, err := pixelgl.NewWindow(pixelgl.WindowConfig{
		Bounds:      pixel.R(0, 0, float64(width), float64(height)),
		Undecorated: false,
		VSync:       true,
		Resizable:   true,
	})
	if err != nil {
		panic(err)
	}

	pic := pixel.PictureDataFromImage(image.NewRGBA(image.Rect(0, 0, width, height)))
	s := &server{image: img{sprite: pixel.NewSprite(pic, pic.Bounds())}}

	go func() {
		fmt.Println(http.ListenAndServe(":8080", s))
	}()
	ticker := time.NewTicker(time.Second)
	go func() {
		tmp := 0
		for t := range ticker.C {
			s.image.Lock()
			tmp = s.image.count
			s.image.count = 0
			s.image.Unlock()
			fmt.Println(t, " ", tmp, " FPS")
		}
	}()

	for !win.Closed() {
		if win.JustPressed(pixelgl.KeyEscape) || win.JustPressed(pixelgl.KeyQ) {
			return
		}

		s.image.RLock()
		s.image.sprite.Draw(win, pixel.IM.Moved(win.Bounds().Center()))
		s.image.RUnlock()

		win.Update()
	}
}
