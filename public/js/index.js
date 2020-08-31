
var myRating = raterJs( {
    {
        starSize:32,
        element:document.querySelector("#rater"),
        rateCallback:function rateCallback(rating, done) {
            this.setRating(rating); 
            done(); 
        }
    }

});