<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<?php if(isset($title)){echo $title;} ?>

<h2>Logged in user: <?php echo Auth::id(); ?></h2>

<h2>Add drinks you've drank:</h2>
<div class="search-container">
<form action="/adddrink" method="post">
    @csrf
    <label for="drink">Drink</label>
    <input type="text" placeholder="Search.." name="drink">
    <br>
    <label for="quantity">Quantity</label>
    <input type="text" name="quantity"></input>
    <br>
    <button type="submit">Submit</button>
</form>
</div>

<table id='drinksShowcase'>
<?php if($cart = session()->get('cart')): ?>
    <?php foreach($cart as $name => $value): ?>
        <?php foreach($value as $drink=>$quantity): ?>
            <tr id="specificRow">
            <td><p id='drinkk' name="drinkk"><?php echo $name; ?></p></td>
            <td style="text-align:right;"><?php echo $quantity; ?></td>
            <td><input id='quantityyy' onchange="changeQuantity()" type="number" name="quantityyy" min="1" max="50" value="<?php echo $quantity; ?>"></td>
            <?php $urlRemove = route('removeFromCart', $name); ?>
            <td style="text-align:center;"><a href="<?php echo $urlRemove; ?>" class="btnRemoveAction"><img src="{{url('/images/icon-delete.png')}}" alt="Remove Item" /></a></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif;?>
</table>

<!-- <button action="/calculate">CALCULATE!</button> -->
<input type="button" onclick="location.href='/calculate'" value="CALCULATE!" />


<?php $urlEmpty = route('emptyCart'); ?>
<a id="btnEmpty" href="<?php echo $urlEmpty; ?>">Empty Cart</a>
<?php $urlLogout = route('logout'); ?>
<a href="<?php echo $urlLogout; ?>">Logout</a>


<script>
function changeQuantity() {
    var x = document.getElementById("quantityyy").value;
    var y = document.getElementById("drinkk").value;
    console.log(x);
    console.log(y);
    let url = "{{ route('changeQuantity', ':id') }}";
    url = url.replace(':id', x);
    // document.location.href=url;
}
// $('#drinksShowcase tr').each(function(){
//     $(this).find('td').each(function(){
//         //do your stuff, you can use $(this) to get current cell
//         console.log($(this));
//     });
// });
// $("#drinksShowcase").on('change', function(e) {
//   var data = $(this).val();
//   console.log(data);
// });
var rows = document.getElementsByTagName('tr');
console.log(rows);
for (var row in rows) {
  row.addEventListener('click', handleEvent);
  // or attachEvent, depends on browser
}
function handleEvent(e) {
  // Do stuff with the row
  console.log(e);
}
</script>
