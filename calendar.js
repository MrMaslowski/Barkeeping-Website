const date = new Date();  // today

// makes anker to last selected days
if(currentDate != ""){
  const currentDateArr = currentDate.split("-");
  date.setFullYear(currentDateArr[0]);
  date.setMonth(currentDateArr[1]-1);
}

const renderCalendar = () => {
  //set date day on 1
  date.setDate(1);

  // select days div for later output
  const monthDays = document.querySelector(".days");

  // get last Day of next month for current month
  const lastDay = new Date(
    date.getFullYear(),
    date.getMonth() + 1,
    0
  ).getDate();

  // get last day of previous month
  const prevLastDay = new Date(
    date.getFullYear(),
    date.getMonth(),
    0
  ).getDate();

  // sets Index of First Day in the month. Starts where previous monthdays end
  const firstDayIndex = date.getDay();

  // sets Index of last Day in following month
  const lastDayIndex = new Date(
    date.getFullYear(),
    date.getMonth() + 1,
    0
  ).getDay();

  // sets number of following days from next month
  const nextDays = 7 - lastDayIndex - 1;

  const months = [
    "Januar",
    "Februar",
    "März",
    "April",
    "Mai",
    "Juni",
    "Juli",
    "August",
    "September",
    "Oktober",
    "November",
    "Dezember",
  ];

  document.querySelector(".date h1").innerHTML = months[date.getMonth()];

  document.querySelector(".date h2").innerHTML = date.getFullYear();

  document.querySelector(".date p").innerHTML = new Date().toDateString();

  // Array, where all day elements are stored and later inserted in html element
  let days = "";

  // put previous day elements in array
  var shift = 1;
  if(firstDayIndex == 0){
    shift = -6
  }
  for (let x = firstDayIndex; x > shift; x--) {
    days += `<div class="prev-date">${prevLastDay - x + 1}</div>`;
  }
  
  // put day elements in array
  for (let i = 1; i <= lastDay; i++) {
    if (i === new Date().getDate() && date.getMonth() === new Date().getMonth() && date.getFullYear() === new Date().getFullYear()) {
      days += `<div class="today">${i}</div>`;
    } else {
      let id = date.getFullYear() +"-" + (date.getMonth() < 9? "0":"") + eval(date.getMonth()+1) +"-"+ (i<10? "0" : "") + i;
      days += `<form method='post' action='index.php?kathegory=calendar&currentDate=${id}'><input type="submit" name="select" class="day" id="${id}" value="${i}"></form>`;
    }
  }

  // put last day elements in array
  for (let j = 1; j <= nextDays+1; j++) {
    days += `<div class="next-date">${j}</div>`;
  }
  
  // add day elements in html days-div
  monthDays.innerHTML = days;

  selectDays();
};


document.querySelector(".prev").addEventListener("click", () => {
  date.setMonth(date.getMonth() - 1);
  renderCalendar();
});

document.querySelector(".next").addEventListener("click", () => {
  date.setMonth(date.getMonth() + 1);
  renderCalendar();
});

// click days
function selectDays(){
  const boxes = document.querySelectorAll('.day');

  for (const box of boxes) {
      if(selectedDays.includes(box.id)){  // mark selected days
        document.getElementById(box.id).classList.add("clicked");
      }
  }
}

renderCalendar();