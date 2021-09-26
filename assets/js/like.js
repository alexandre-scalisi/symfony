// désolé c'est le bordel

const likes = document.querySelectorAll('.like');
if (likes) {
  likes.forEach(l => l.addEventListener('click', function(e) {
    e.preventDefault();
    const curLike = this.dataset.like;
    if(!curLike) return;
    const parent = this.closest('.likes-parent');
    const [dislike, like] = parent.querySelectorAll('.like');
    const avg = parent.querySelector('span');

    const curAvg = +avg.textContent;
    fetch(this.href, {
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      method: "POST",
      body: curLike
    }).then(res => res.json()
    ).then(data => {
      console.log(data)
    

      if(data.selected === 'none') {
        dislike.classList.remove('underline');
        like.classList.remove('underline');

      } else if(data.selected){
        dislike.classList.remove('underline');
        like.classList.add('underline');
      } else {
        like.classList.remove("underline");
        dislike.classList.add("underline");
      }
      const areBothUnselected = [dislike, like].every(
        (x) => !x.classList.contains("underline")
      );
      
      if(areBothUnselected) {
        if(curLike === "true") avg.textContent = curAvg - 1;
        else avg.textContent = curAvg + 1;

      } else {
        if(data.new) {
          if (curLike === "true") avg.textContent = curAvg + 1;
          else avg.textContent = curAvg - 1;
        } else {
          if (curLike === "true") avg.textContent = curAvg + 2;
          else avg.textContent = curAvg - 2;
        }
      }
    
      
    }
    ).catch(err => console.log(err));
  })
)}