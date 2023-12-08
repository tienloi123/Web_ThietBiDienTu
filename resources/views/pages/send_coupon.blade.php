<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<style>
		body{
			font-family: Arial;
		}
		.coupon {
			border: 5px dotted #bbb;
			width: 80%;
			border-radius: 15px;
			margin: 0 auto;
			max-width: 600px;

		}
		.container {
			padding: 2px 16px;
			background-color: #f1f1f1;
		}
		.promo {
			background: #ccc;
			padding: 3px;
		}
		.expire {
			color: red;
		}
		p.code {
			text-align: center;
			font-size: 20px;

		}
		p.expire {
			text-align: center;
		}
		h2.note {
			text-align: center;
			font-size: large;
			text-decoration: underline;
		}
	</style>
</head>
<body>
	<div>
		<h3>Mã khuyến mãi dành cho khách thường từ shop <a target="_blank" href="http://localhost:81/blog">MY SHOP </a></h3>

	</div>
	<div class="container" style="background-color: white">
		<h2 class="note"><b><i>
			@if($coupon['coupon_condition']==1)
			Giảm {{$coupon['coupon_number']}}%
			@else
			Giảm {{number_format($coupon['coupon_number'],0,',','.')}}k
			@endif
			Cho tổng đơn hàng đặt mua
		
		</i></b></h2>
		<p>Quý khách từng mua hàng tại shop <a target="_blank" style="color:red" href="http://localhost:81/blog">DoanTienLoi.com</a>nếu đã có tài khoản xin vui lòng <a target="_blank" style="color:red" href="http://localhost:81/blog/dang-nhap">Đăng nhập</a>vào tài khoản để mua hàng và nhập lại mã code phía dưới để được giảm giá mua hàng, xin cảm ơn quý khách.Chúc quý khách thật nhiều sức khỏe và bình an trong cuộc sống.</p>
	</div>
	<div class="container">
		<p class="code">Sử dụng Mã giảm giá sau <span class="promo">{{$coupon['coupon_code']}} </span>Với chỉ {{$coupon['coupon_time']}} mã giảm giá, nhanh tay kẻo hết</p>
		<p class="expire">Ngày bắt đầu: {{$coupon['start_coupon']}} / Ngày hết hạn code: {{$coupon['end_coupon']}} </p>
	</div>
</body>
</html>