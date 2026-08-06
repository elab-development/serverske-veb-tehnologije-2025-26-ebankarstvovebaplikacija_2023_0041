<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
    <style>
        #name-container {
            padding: 7px;
            height: 70px;
            width: 250px;
            border: 2px solid black;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .account-container {
            position: relative;
            padding: 5px;
            height: 15px;
            width: 270px;
            margin-bottom: 10px;
            border: 2px solid black;
        }

        #amount-container {
            position: relative;
            height: 15px;
            padding: 5px;
            width: 100px;
            margin-bottom: 10px;
            border: 2px solid black;
        }

        #currency-container {
            position: relative;
            height: 15px;
            padding: 5px;
            width: 50px;
            margin-bottom: 10px;
            border: 2px solid black;
        }

        #title {
            font-size: 25px;
            font-weight: bold;
        }

        #amount-currency-container {
            display: flex;
            gap: 10px;
        }

        #date-time-container {
            display: flex;
            gap: 10px;
        }

        #date-container {
            width: 100px;
            text-align: center;
        }

        #date-value-container {
            width: 100px;

            border-bottom: 3px solid black;
        }

        #time-container {
            width: 100px;
            text-align: center;
        }

        #time-value-container {
            width: 100px;

            border-bottom: 3px solid black;
        }

        #category-container {
            position: relative;
            padding: 5px;
            height: 15px;
            width: 250px;
            margin-bottom: 10px;
            border: 2px solid black;
        }

        #td1{
            margin-right:10px;
        }

    </style>
</head>

<body>

    <table width="100%">
        <div id="title">TRANSACTION</div>
        <div>Transaction identification number: <span>{{ $id }}<span></div>
        <tr>
            <td width="35%" valign="top" id="td1" style="padding-right:15px;">
                <div>
                    <div style="font-weight:bold;margin-bottom:-5px;">Name: </div>
                    <div id="name-container">{{ $name }}</div>
                </div>
                <div>
                    <div style="font-weight:bold; margin-bottom:5px;">Category: </div>
                    <div id="category-container">{{ $category }}</div>
                </div>
            </td>

            <td width="50%" valign="top">
                <div>
                    <div style="font-weight:bold;margin-bottom:5px;">Sender account: </div>
                    <div class="account-container">{{ $senderAccount }}</div>
                </div>
                <div>
                    <div style="font-weight:bold;margin-bottom:5px;">Receiver account: </div>
                    <div class="account-container">{{ $receiverAccount }}</div>
                </div>
            </td>

            <td width="25%" valign="top">
                <div id="amount-currency-container">
                    <div>
                        <div style="font-weight:bold;margin-bottom:5px;">Currency: </div>
                        <div id="currency-container">{{ $currency }}</div>
                    </div>
                    <div>
                        <div style="font-weight:bold;margin-bottom:5px;">Amount: </div>
                        <div id="amount-container">{{ $amount }}</div>
                    </div>
                </div>
                <div id="date-time-container">
                    <div id="date-container">
                        <div id="date-value-container">{{ $date }}</div>
                        date
                    </div>

                    <div id="time-container">
                        <div id="time-value-container">{{ $time }}</div>
                        time
                    </div>
                </div>

            </td>
        </tr>
    </table>












</body>

</html>