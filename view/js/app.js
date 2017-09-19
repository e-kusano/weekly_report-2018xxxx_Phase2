//アンカー
function sub_redirect(base, con, met, pars) {

	var param = base + con;

	if (met != '' && met != undefined) {
		param += '/' + met;
	}

	if (pars != '' && pars != undefined) {
		param += '/' + pars;
	}

	location.href = param;
}