let checkComplete = document.getElementById("lesson-complete")
checkComplete.addEventListener("change", (e) => {
  var previousUrl = window.location.href
  var url = new URL(previousUrl)
  const urlSearchParams = new URLSearchParams(window.location.search)
  urlSearchParams.set(checkComplete.name, checkComplete.value)
  url.search = urlSearchParams.toString()
  location.replace(url)
})
