const likes = document.querySelectorAll('.like');

const toggleClass = (data, likeEl) => {

  const parent = likeEl.closest(".likes-parent");
  const [dislike, like] = parent.querySelectorAll("[class*='thumbs'");
  console.log('test')
  if (data.selected === "none") {
    dislike.classList.remove("fas");
    like.classList.remove("fas");
    dislike.classList.add("far");
    like.classList.add("far");
  } else if (data.selected) {
    dislike.classList.remove("fas");
    dislike.classList.add("far");
    like.classList.remove("far");
    like.classList.add("fas");
  } else {
    dislike.classList.remove("far");
    dislike.classList.add("fas");
    like.classList.remove("fas");
    like.classList.add("far");
  }
  document.getElementById("vote-average").textContent = data.avg;
}


if (likes) {
  likes.forEach(l => l.addEventListener('click', function(e) {
    e.preventDefault();
    fetch(this.href, {
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      method: "POST",
      body: this.dataset.like,
    })
      .then((res) => {
        if (res.status === 403)
          throw new Error("Vous devez être connecté pour voter");
        return res.json();
      })
      .then((data) => {
        console.log(data)
        toggleClass(data, this);
      })
      .catch((err) => {
        alert(err.message);
      });
  })
)}

