<form action='{{route("lipila.collect")}}' method="post">
    @csrf
    <input type="text" placeholder="msisdn" name="MSISDN">
    <input type="text" placeholder="amount" name="AMOUNT">
    <button type="submit">submit</button>
</form>
