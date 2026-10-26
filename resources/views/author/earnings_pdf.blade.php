<!DOCTYPE html>
<html lang="th">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>รายงานรายได้</title>
    <style>
        @font-face {
        font-family: 'THSarabunNew';
        font-style: normal;
        font-weight: normal;
        src: url("{{ str_replace('\\', '/', public_path('fonts/THSarabunNew.ttf')) }}") format('truetype');
    }
    @font-face {
        font-family: 'THSarabunNew';
        font-style: normal;
        font-weight: bold;
        src: url("{{ str_replace('\\', '/', public_path('fonts/THSarabunNew_Bold.ttf')) }}") format('truetype');
    }
    @font-face {
        font-family: 'THSarabunNew';
        font-style: italic;
        font-weight: normal;
        src: url("{{ str_replace('\\', '/', public_path('fonts/THSarabunNew_Italic.ttf')) }}") format('truetype');
    }
    @font-face {
        font-family: 'THSarabunNew';
        font-style: italic;
        font-weight: bold;
        src: url("{{ str_replace('\\', '/', public_path('fonts/THSarabunNew_BoldItalic.ttf')) }}") format('truetype');
    }

    /* นำฟอนต์มาตั้งค่าเริ่มต้นให้กับหน้า PDF */
    body {
        font-family: 'THSarabunNew', sans-serif; 
        font-size: 18px; 
        color: #333;
    }

        /* ... สไตล์อื่นๆ ของคุณ ... */
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <h2>รายงานรายได้เหรียญจากนิยาย</h2>

    <div class="total-box">
        <strong>สรุปยอดเหรียญรวมที่ได้รับต่อวัน:</strong>
        <ul>
            @forelse($dailyTotals as $date => $coins)
                <li>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} : {{ number_format($coins) }} เหรียญ</li>
            @empty
                <li>ยังไม่มีรายได้ในช่วงที่เลือก</li>
            @endforelse
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th>วันที่</th>
                <th>นิยาย</th>
                <th class="text-right">เหรียญที่ได้รับ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($earnings as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->earned_on)->format('d/m/Y') }}</td>
                    <td>{{ $item->novel_title }}</td>
                    <td class="text-right">{{ number_format($item->coins) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align:center;">ไม่มีข้อมูล</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>