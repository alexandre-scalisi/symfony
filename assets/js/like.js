const likes = document.querySelectorAll(".like");

const toggleClass = (item, active) => {
  if(active) {
    item.classList.add('fas')
    item.classList.remove('far')
  } else {
    item.classList.add("far");
    item.classList.remove("fas");
  }
}

const handleLike = (data, likeEl) => {
  const parent = likeEl.closest(".likes-parent");
  const [dislike, like] = parent.querySelectorAll("[class*='thumbs'");
  console.log(data.avg)
  toggleClass(dislike, data.selected === false)
  toggleClass(like, data.selected === true)
 
  parent.querySelector("#vote-average").textContent = data.avg;
};

if (likes) {
  likes.forEach((l) =>
    l.addEventListener("click", function (e) {
      e.preventDefault();
      const href = this.href;
      fetch(href, {
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        method: "POST",
      })
        .then((res) => {
          if (res.status === 403)
            throw new Error("Vous devez être connecté pour voter");
          return res.json();
        })
        .then((data) => {
          handleLike(data, this);
        })
        .catch((err) => {
          alert(err.message);
        });
    })
  );
}
