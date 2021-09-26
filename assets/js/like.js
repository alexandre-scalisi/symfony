const likes = document.querySelectorAll('.like');
if (likes) {
  likes.forEach(l => l.addEventListener('click', function(e) {
    e.preventDefault();

    fetch(this.href, {
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      method: "POST",
      body: "test"
    }).then(res => res.json()
    ).then(data => console.log(data)
    ).catch(err => console.log(err));
  })
)}