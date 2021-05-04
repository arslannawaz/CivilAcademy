<html>
<head>
    <title>Success</title>
</head>

    <h1>Transaction has been made successfully!</h1>

    <p>Transaction ID: {{$transaction_id ?? ''}}</p> 

    <input id='status' hidden value="success" >
    <input id='transaction_id' hidden value="{{$transaction_id}}" >

</html>