<?php
    $this->layout = 'layout.php';
    $this->ViewData['title'] = 'Trang chủ';
?>
    <div id="slider-wrapper">
        <div id="slider">
            <img src="/images/banner/banner (1).jpg"/>
            <img src="/images/banner/banner (1).png"/>
            <img src="/images/banner/banner (2).jpg"/>
            <img src="/images/banner/banner (2).png"/>
            <img src="/images/banner/banner (3).png"/>
        </div>
        <div id="slider-next">&gt;</div>
        <div id="slider-previous">&lt;</div>
        <div id="slider-indicator">
            <div class="slider-indicator-index active"></div>
                <div class="slider-indicator-index"></div>
                <div class="slider-indicator-index"></div>
                <div class="slider-indicator-index"></div>
                <div class="slider-indicator-index"></div>
        </div>
    </div>
    <div id="content-wrapper">
        
    </div>

                <script>
                    var sliderController = new (function(){
                        this.images = document.querySelectorAll('div#slider img');
                        this.totalImage = this.images.length;
                        for(var i=0; i<this.images.length; i++){
                            if(i==0){
                                this.images[i].onload = function(e){
                                    this.style.marginLeft = -this.width/2 + 'px';
                                }
                            }else{
                                this.images[i].onload = function(e){
                                    this.style.marginLeft = -this.width/2 + 'px';
                                    this.style.display = 'none';
                                }
                            }
                            if(this.images[i].complete){
                                this.images[i].onload();
                            }
                        }
                        this.indexButtons = document.querySelectorAll('div.slider-indicator-index');
                        for(var i=0; i<this.indexButtons.length; i++){
                            this.indexButtons[i].index = i;
                            this.indexButtons[i].onclick = function(e){
                                if(this.index>sliderController.currentImage){
                                    sliderController.images[this.index].style.animationName = 'righttoleft';
                                }else{
                                    sliderController.images[this.index].style.animationName = 'lefttoright';
                                }
                                if(this.index!==sliderController.currentImage){
                                    sliderController.clearActive();
                                    sliderController.setActive(this.index);
                                }
                            }
                        }
                        this.nextButton = document.querySelector('div#slider-next');
                        this.nextButton.onclick = function(e){
                            sliderController.next();
                        }
                        this.previousButton = document.querySelector('div#slider-previous');
                        this.previousButton.onclick = function(e){
                            sliderController.previous();
                        }
                        this.currentImage = 0;
                        this.setActive = function(n){
                            this.indexButtons[n].classList.add('active');
                            this.currentImage = n;
                            this.images[this.currentImage].style.display = 'inline-block';
                        }
                        this.clearActive = function(){
                            this.indexButtons[this.currentImage].classList.remove('active');
                            this.images[this.currentImage].style.display = 'none';
                        }
                        this.next = function(){
                            this.images[this.currentImage].style.display = 'none';
                            this.clearActive();
                            this.currentImage = (this.currentImage + 1)%this.totalImage;
                            this.images[this.currentImage].style.animationName = 'righttoleft';
                            this.setActive(this.currentImage);
                        }
                        this.previous = function(){
                            this.images[this.currentImage].style.display = 'none';
                            this.clearActive();
                            this.currentImage = (this.currentImage + this.totalImage - 1)%this.totalImage;
                            this.images[this.currentImage].style.animationName = 'lefttoright';
                            this.setActive(this.currentImage);
                        }
                        window.setInterval(function(e){
                            sliderController.next();
                        },5000);
                    })();
                </script>