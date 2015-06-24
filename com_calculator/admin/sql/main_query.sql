#select * from calc_delivery_weight_price

select r.*,wp.* 
from calc_delivery_city cf
	join calc_delivery_city ct on ct.city = 500
	left join calc_delivery_direction2zone d2z on d2z.city_from = COALESCE(cf.parent, cf.city) 
						and d2z.city_to = COALESCE(ct.parent, ct.city)
	left join calc_delivery_direction2zone d2z_exact on d2z_exact.city_from = cf.city 
						and d2z_exact.city_to = ct.city
	left join calc_delivery_zone z on z.zone = d2z.zone or d2z_exact.zone = z.zone
	join calc_delivery_rate r on 
						(r.zone = z.zone and r.provider = z.provider)
	join calc_delivery_weight_price wp on 
						wp.rate = r.rate 
						and wp.from <= 5 and wp.to > 5
where
	cf.city = 1

union all

select r.*, wp.* 
from calc_delivery_city cf
	join calc_delivery_city ct on ct.city = 500
	join calc_delivery_rate r on 
						(r.city_from = 1 and r.city_to = 500)
	join calc_delivery_weight_price wp on 
						wp.rate = r.rate 
						and wp.from <= 5 and wp.to > 5
where
	cf.city = 1