function switchMainTab(tabId) {
  const lessonsBtn = document.getElementById("btn-tab-lessons");
  const reviewsBtn = document.getElementById("btn-tab-reviews");
  const studentLevelBtn = document.getElementById("btn-tab-student_level");
  const lessonsContent = document.getElementById("content-main-lessons");
  const reviewsContent = document.getElementById("content-main-reviews");
  const studentLevelContent = document.getElementById(
    "content-main-student_level",
  );

  if (tabId === "lessons") {
    lessonsBtn.className =
      "pb-3 text-sm font-bold border-b-2 border-[#FEB008] text-[#FEB008] transition-all";
    reviewsBtn.className =
      "pb-3 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all";
    studentLevelBtn.className =
      "pb-3 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all";
    lessonsContent.classList.remove("hidden");
    reviewsContent.classList.add("hidden");
    studentLevelContent.classList.add("hidden");
  } else if (tabId === "reviews") {
    reviewsBtn.className =
      "pb-3 text-sm font-bold border-b-2 border-[#FEB008] text-[#FEB008] transition-all";
    lessonsBtn.className =
      "pb-3 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all";
    studentLevelBtn.className =
      "pb-3 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all";
    reviewsContent.classList.remove("hidden");
    lessonsContent.classList.add("hidden");
    studentLevelContent.classList.add("hidden");
  } else if (tabId === "student_level") {
    studentLevelBtn.className =
      "pb-3 text-sm font-bold border-b-2 border-[#FEB008] text-[#FEB008] transition-all";
    lessonsBtn.className =
      "pb-3 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all";
    reviewsBtn.className =
      "pb-3 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all";
    reviewsContent.classList.add("hidden");
    lessonsContent.classList.add("hidden");
    studentLevelContent.classList.remove("hidden");
    filterStudentTerm("all");
  }
}

function filterTerm(term) {
  const buttons = document.querySelectorAll(".term-btn");
  buttons.forEach((btn) => {
    btn.className =
      "term-btn px-4 py-1.5 text-xs font-semibold rounded-lg text-gray-500 hover:text-gray-700 transition-all";
  });
  document.getElementById(`btn-sub-${term}`).className =
    "term-btn px-4 py-1.5 text-xs font-bold rounded-lg bg-[#FEB008] text-white transition-all shadow-sm";
  const items = document.querySelectorAll(".accordion-item");
  items.forEach((item) => {
    item.querySelector(".accordion-body").style.maxHeight = "0px";
    item.querySelector(".arrow-icon").classList.remove("rotate-180");

    if (term === "all" || item.getAttribute("data-term") === term) {
      item.style.display = "block";
    } else {
      item.style.display = "none";
    }
  });
}

function toggleAccordion(header) {
  const currentItem = header.parentElement;
  const currentBody = currentItem.querySelector(".accordion-body");
  const currentArrow = currentItem.querySelector(".arrow-icon");

  const allItems = document.querySelectorAll(".accordion-item");

  allItems.forEach((item) => {
    if (item !== currentItem) {
      item.querySelector(".accordion-body").style.maxHeight = "0px";
      item.querySelector(".arrow-icon").classList.remove("rotate-180");
    }
  });

  if (
    currentBody.style.maxHeight === "0px" ||
    currentBody.style.maxHeight === ""
  ) {
    currentBody.style.maxHeight = "1000px";
    currentArrow.classList.add("rotate-180");
  } else {
    currentBody.style.maxHeight = "0px";
    currentArrow.classList.remove("rotate-180");
  }
}

const studentData = {
  all: {
    generalLevel: "ممتاز",
    generalLevelPercent: "92%",
    timeSpent: "18 ساعة و 45 دقيقة",
    attendanceLessons: "52 / 65",
    attendancePercent: 80,
    homeworkCorrect: 145,
    homeworkWrong: 12,
    homeworkRate: "94%",
    examsAvg: "18.5 / 20",
    exams: [
      {
        id: 1,
        title: "اختبار الوحدة الأولى",
        time: "منذ أسبوعين",
        score: "19 / 20",
        grade: "ممتاز",
        gradeColor: "#00AA6C",
      },
      {
        id: 2,
        title: "اختبار الدرس الثالث",
        time: "منذ 3 أيام",
        score: "18 / 20",
        grade: "جيد جداً",
        gradeColor: "#008B5B",
      },
    ],
    strengths: ["الجبر والعمليات الحسابية", "سرعة حل المسائل اللفظية"],
    opportunities: [
      "التركيز في خطوات البرهان الهندسي",
      "مراجعة قوانين حساب المثلثات",
    ],
  },
  term1: {
    generalLevel: "ممتاز",
    generalLevelPercent: "95%",
    timeSpent: "10 ساعات و 15 دقيقة",
    attendanceLessons: "30 / 32",
    attendancePercent: 94,
    homeworkCorrect: 80,
    homeworkWrong: 4,
    homeworkRate: "95%",
    examsAvg: "19 / 20",
    exams: [
      {
        id: 1,
        title: "اختبار الوحدة الأولى",
        time: "منذ أسبوعين",
        score: "19 / 20",
        grade: "ممتاز",
        gradeColor: "#00AA6C",
      },
    ],
    strengths: ["الجبر والعمليات الحسابية"],
    opportunities: ["التركيز في خطوات البرهان الهندسي"],
  },
  term2: {
    generalLevel: "جيد جداً",
    generalLevelPercent: "89%",
    timeSpent: "8 ساعات و 30 دقيقة",
    attendanceLessons: "22 / 33",
    attendancePercent: 67,
    homeworkCorrect: 65,
    homeworkWrong: 8,
    homeworkRate: "89%",
    examsAvg: "18 / 20",
    exams: [
      {
        id: 2,
        title: "اختبار الدرس الثالث",
        time: "منذ 3 أيام",
        score: "18 / 20",
        grade: "جيد جداً",
        gradeColor: "#008B5B",
      },
    ],
    strengths: ["سرعة حل المسائل اللفظية"],
    opportunities: ["مراجعة قوانين حساب المثلثات"],
  },
};

function filterStudentTerm(term) {
  // Update sub-filter active styles
  const buttons = document.querySelectorAll(".student-term-btn");
  buttons.forEach((btn) => {
    btn.className =
      "student-term-btn px-4 py-1.5 text-xs font-semibold rounded-lg text-gray-500 hover:text-gray-700 transition-all";
  });

  const activeBtn = document.getElementById(`btn-student-${term}`);
  if (activeBtn) {
    activeBtn.className =
      "student-term-btn px-4 py-1.5 text-xs font-bold rounded-lg bg-[#FEB008] text-white transition-all shadow-sm";
  }

  // Trigger content fade animation
  const container = document.getElementById("student-level-container");
  if (container) {
    container.classList.remove("animate-fade-in-up");
    // Trigger layout reflow to restart animation
    void container.offsetWidth;
    container.classList.add("animate-fade-in-up");
  }

  // Load data
  const data = studentData[term];
  if (!data) return;

  // Update DOM elements
  document.getElementById("student-general-level").textContent =
    data.generalLevel;
  document.getElementById("student-general-level-percent").textContent =
    data.generalLevelPercent;
  document.getElementById("student-time-spent").textContent = data.timeSpent;
  document.getElementById("student-lessons-ratio").textContent =
    data.attendanceLessons;
  document.getElementById("student-homework-correct").textContent =
    data.homeworkCorrect;
  document.getElementById("student-homework-wrong").textContent =
    data.homeworkWrong;
  document.getElementById("student-homework-rate").textContent =
    data.homeworkRate;
  document.getElementById("student-exams-avg").textContent = data.examsAvg;

  // Animate linear progress bar
  const progressBar = document.getElementById(
    "student-attendance-progress-bar",
  );
  if (progressBar) {
    progressBar.style.width = "0%";
    setTimeout(() => {
      progressBar.style.width = `${data.attendancePercent}%`;
    }, 50);
  }

  // Animate circular progress ring
  const circleProgress = document.getElementById("student-circle-progress");
  const attendancePercentText = document.getElementById(
    "student-attendance-percent",
  );
  if (circleProgress) {
    circleProgress.style.strokeDasharray = `0, 100`;
    setTimeout(() => {
      circleProgress.style.strokeDasharray = `${data.attendancePercent}, 100`;
    }, 50);
  }
  if (attendancePercentText) {
    attendancePercentText.textContent = `${data.attendancePercent}%`;
  }

  // Render Exams
  const examsList = document.getElementById("student-exams-list");
  if (examsList) {
    if (data.exams.length === 0) {
      examsList.innerHTML = `<div class="text-center py-4 text-xs text-gray-400">لا توجد امتحانات مضافة بعد.</div>`;
    } else {
      examsList.innerHTML = data.exams
        .map(
          (exam) => `
        <div class="flex items-center justify-between p-4 bg-[#F8F7FF] rounded-2xl border border-[#ECECFF]/50 hover:border-[#FEB008]/25 transition-all duration-300 shadow-sm hover:shadow-md">
             <div class="flex items-center gap-3">
                 <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center font-bold text-gray-700 text-sm shadow-sm border border-gray-100">${exam.id}</div>
                 <div class="text-right">
                     <h5 class="text-xs font-bold text-gray-800">${exam.title}</h5>
                     <span class="text-[10px] text-gray-400 block mt-0.5">${exam.time}</span>
                 </div>
             </div>
             <div class="text-left">
                 <span class="text-xs font-bold text-[#FEB008] block">${exam.score}</span>
                 <span class="text-[10px] font-bold block mt-0.5" style="color: ${exam.gradeColor};">${exam.grade}</span>
             </div>
        </div>
      `,
        )
        .join("");
    }
  }

  // Render Strengths
  const strengthsList = document.getElementById("student-strengths-list");
  if (strengthsList) {
    strengthsList.innerHTML = data.strengths
      .map(
        (strength) => `
      <li class="flex items-center gap-2">
          <span class="w-1.5 h-1.5 rounded-full bg-[#00AA6C] shrink-0"></span>
          <span>${strength}</span>
      </li>
    `,
      )
      .join("");
  }

  // Render Opportunities
  const opportunitiesList = document.getElementById(
    "student-opportunities-list",
  );
  if (opportunitiesList) {
    opportunitiesList.innerHTML = data.opportunities
      .map(
        (opportunity) => `
      <li class="flex items-center gap-2">
          <span class="w-1.5 h-1.5 rounded-full bg-[#D97706] shrink-0"></span>
          <span>${opportunity}</span>
      </li>
    `,
      )
      .join("");
  }
}
