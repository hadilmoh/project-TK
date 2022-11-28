//============percent value============//

const span = document.getElementById('value-of-percent');

// 👇️ to get text of span and save in percent var
const percent = span.textContent;

  let progressBar = document.querySelector(".circular-progress");
  let valueContainer = document.querySelector(".value-container");

  let progressValue = 0;
  let progressEndValue = percent;
  let speed = 50;

  let progress = setInterval(() => {
    progressValue++;
    valueContainer.textContent = `${progressValue}%`;
    progressBar.style.background = `conic-gradient(
        #4d5bf9 ${progressValue * 3.6}deg,
        #cadcff ${progressValue * 3.6}deg
    )`;
    if (progressValue == progressEndValue) {
      clearInterval(progress);
    }
  }, speed);

//============percent value============//