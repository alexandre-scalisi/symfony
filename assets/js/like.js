const likes = document.querySelectorAll('.like');

const toggleClass = (data, likeEl) => {

  const parent = likeEl.closest(".likes-parent");
  const [dislike, like] = parent.querySelectorAll(".like");

  if (data.selected === "none") {
    dislike.classList.remove("underline");
    like.classList.remove("underline");
  } else if (data.selected) {
    dislike.classList.remove("underline");
    like.classList.add("underline");
  } else {
    like.classList.remove("underline");
    dislike.classList.add("underline");
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
        toggleClass(data, this);
      })
      .catch((err) => {
        alert(err.message);
      });
  })
)}

