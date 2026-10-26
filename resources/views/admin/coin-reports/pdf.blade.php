<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        body {
            font-family: "THSarabunNew", sans-serif;
            font-size: 16px;
            color: #333;
        }
        h2, h3 {
            text-align: center;
            margin-bottom: 5px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* สไตล์สำหรับกล่องสรุปยอด */
        .summary-table {
            width: 100%;
            margin-bottom: 20px;
            border: none;
        }
        .summary-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            background-color: #f9f9f9;
        }
        .summary-value {
            font-size: 20px;
            font-weight: bold;
        }
        .text-red { color: #dc2626; }
        .text-green { color: #16a34a; }
        .text-blue { color: #2563eb; }

        /* สไตล์สำหรับตารางข้อมูล */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 6px;
        }
        .data-table th {
            background-color: #e5e7eb;
            text-align: left;
        }
        .data-table th.text-center { text-align: center; }
        .data-table th.text-right { text-align: right; }
    </style>
</head>
<body>

    <h2>รายงานเหรียญสำหรับผู้ดูแล</h2>
    <p class="text-center">ข้อมูลช่วงวันที่ {{ $start->format('d/m/Y') }} – {{ $end->format('d/m/Y') }}</p>

    <!-- สรุปยอดรวม -->
    <table class="summary-table">
        <tr>
            <td>
                <div>ยอดใช้เหรียญซื้อตอน</div>
                <div class="summary-value text-red">{{ number_format($coinsSpent) }} เหรียญ</div>
            </td>
            <td>
                <div>ยอดเหรียญจากการโอนที่อนุมัติ</div>
                <div class="summary-value text-green">{{ number_format($coinsToppedUp) }} เหรียญ</div>
            </td>
            <td>
                <div>ยอดเงินโอนที่อนุมัติ</div>
                <div class="summary-value text-blue">฿{{ number_format($approvedTopupMoney, 2) }}</div>
            </td>
        </tr>
    </table>

    <!-- ตารางประวัติการโอนเงินซื้อเหรียญ -->
    <h3>ประวัติการโอนเงินซื้อเหรียญ</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>วันเวลา</th>
                <th>ผู้โอน</th>
                <th class="text-right">บาท</th>
                <th class="text-right">เหรียญ</th>
                <th>สถานะ</th>
                <th>ผู้ตรวจ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topups as $topup)
            <tr>
                <td>{{ $topup->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $topup->user->username }}</td>
                <td class="text-right">฿{{ number_format($topup->amount, 2) }}</td>
                <td class="text-right">{{ number_format($topup->coins_to_receive) }}</td>
                <td>{{ ['pending'=>'รอตรวจ','approved'=>'อนุมัติ','rejected'=>'ปฏิเสธ'][$topup->status] }}</td>
                <td>{{ $topup->admin?->username ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-gray-500">ไม่มีรายการ</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- ตารางประวัติการใช้เหรียญซื้อตอน -->
    <h3>ประวัติการใช้เหรียญซื้อตอน</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>วันเวลา</th>
                <th>ผู้อ่าน</th>
                <th>นิยาย</th>
                <th class="text-center">ตอนที่</th>
                <th class="text-right">ใช้เหรียญ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $purchase)
            <tr>
                <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $purchase->user->username }}</td>
                <td>{{ $purchase->chapter->novel->title }}</td>
                <td class="text-center">{{ $purchase->chapter->chapter_number }}</td>
                <td class="text-right">{{ number_format($purchase->price_paid) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-gray-500">ไม่มีรายการ</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>