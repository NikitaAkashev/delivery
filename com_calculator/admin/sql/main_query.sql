#select * from calc_delivery_weight_price
select 
	@weight := 4,
	@city_from := 1,
	@city_to := 500,
	@assessed_value := 1000,
	@width := 100,
	@length := 20,
	@height := 50,
	@user := 1,
	@volume := @width * @length * @height
;

select
	base.*,
	vp.from avp_bottom,
	vp.base_price avp_base_price,
	vp.overprice_percent,
	d.factor discount_factor,
	t.margin,
	t.name tariff_name,
	t.code tariff_code,
	p.dimension_limit,
	p.weight_limit,
	p.oversize_limit_factor,
	p.min_assessed_price,
	p.name provider_name,
	cf2t.min_time cf_min_time,
	cf2t.max_time cf_max_time,
	ct2t.min_time ct_min_time,
	ct2t.max_time ct_max_time,
	coalesce(ff.factor, 1) cf_factor,
	coalesce(ft.factor, 1) ct_factor,
	dt.name delivery_type_name,
	dt.code delivery_type_code,
	cp.price courier_price
from(
	select 
		cf.city city_from,
		ct.city city_to,
		r.rate,
		r.tariff,
		r.provider,
		r.delivery_hours,
		r.min_days,
		r.max_days,
		wp.base_price,
		wp.overweight_cost,
		wp.from weight_bottom,
		greatest(@weight, @volume/p.volume_weight_divider) real_weight
	from calc_delivery_city cf
		join calc_delivery_city ct on ct.city = @city_to
		left join calc_delivery_direction2zone d2z on d2z.city_from = COALESCE(cf.parent, cf.city) 
							and d2z.city_to = COALESCE(ct.parent, ct.city)
		left join calc_delivery_direction2zone d2z_exact on d2z_exact.city_from = cf.city 
							and d2z_exact.city_to = ct.city
		join calc_delivery_zone z on z.zone = d2z.zone or d2z_exact.zone = z.zone
		join calc_delivery_rate r on 
							(r.zone = z.zone and r.provider = z.provider)
		join calc_delivery_provider p on p.provider = r.provider
		join calc_delivery_weight_price wp on 
							wp.rate = r.rate 
							and wp.from <= greatest(@weight, @volume/p.volume_weight_divider) 
							and wp.to > greatest(@weight, @volume/p.volume_weight_divider)
	where
		cf.city = @city_from

	union all

	select
		cf.city city_from,
		ct.city city_to,
		r.rate,
		r.tariff,
		r.provider,
		r.delivery_hours,
		r.min_days,
		r.max_days,
		wp.base_price,
		wp.overweight_cost,
		wp.from weight_bottom,
		greatest(@weight, @volume/p.volume_weight_divider) real_weight
	from calc_delivery_city cf
		join calc_delivery_city ct on ct.city = @city_to
		join calc_delivery_rate r on 
							(r.city_from = cf.city and r.city_to = ct.city)
		join calc_delivery_provider p on p.provider = r.provider
		join calc_delivery_weight_price wp on 
							wp.rate = r.rate 
							and wp.from <= greatest(@weight, @volume/p.volume_weight_divider) 
							and wp.to > greatest(@weight, @volume/p.volume_weight_divider)
	where
		cf.city = @city_from
) base
	join calc_delivery_provider p on p.provider = base.provider
	join calc_delivery_tariff t on t.tariff = base.tariff
	left join calc_delivery_delivery_type2tariff dt2t on dt2t.tariff = t.tariff
	left join calc_delivery_delivery_type dt on dt.delivery_type = dt2t.delivery_type
	join calc_delivery_courier_price cp on 
					cp.tariff = t.tariff
					and cp.weight_from <= base.real_weight
					and cp.weight_to > base.real_weight
	left join calc_delivery_city_factor ff on 
					ff.city = base.city_from
					and ff.tariff = t.tariff
	left join calc_delivery_city_factor ft on 
					ft.city = base.city_to
					and ft.tariff = t.tariff
	left join calc_delivery_city2delivery_time cf2t on 
					cf2t.provider = base.provider 
					and cf2t.city = base.city_from
	left join calc_delivery_city2delivery_time ct2t on 
					ct2t.provider = base.provider 
					and ct2t.city = base.city_to
	left join calc_delivery_assessed_value_price vp on 
					vp.from <= @assessed_value 
					and vp.to > @assessed_value
					and vp.tariff = base.tariff
	left join calc_delivery_discount d on
					d.city_from = base.city_from
					and d.city_to = base.city_to
					and (d.user is null or d.user = @user)
