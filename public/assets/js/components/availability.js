document.addEventListener("click",function(e){

if(e.target.classList.contains("availability-add")){

let day=e.target.dataset.day;

let box=e.target.previousElementSibling;

let index = box.querySelectorAll(".availability-row").length;

box.insertAdjacentHTML(

"beforeend",

`
<div class="availability-row">

<input
type="time"
name="availability[${day}][${index}][start_time]">

<input
type="time"
name="availability[${day}][${index}][end_time]">

<label>

<input
type="checkbox"
name="availability[${day}][${index}][is_closed]"
value="1">

تعطیل

</label>

<button
type="button"
class="availability-remove">

−

</button>

</div>

`

);

}

if(e.target.classList.contains("availability-remove")){

e.target.closest(".availability-row").remove();

}

});