local redis = require "resty.redis".new()
local template = require "resty.template"
local wait_page = template.new "wait.html"
local err_page = template.new "error.json"

ngx.header.content_type = "application/json;charset=utf8;"
if ngx.var.server_name=='m.lsa0.cn' then
    local ok, err = redis:connect("r-m5e1c7e6a2a727d4.redis.rds.aliyuncs.com", 6379)
    local ok, err = redis:auth("Acctrue886")
    if not ok then
        ngx.say("failed to connect: ", err)
        return
    end
elseif ngx.var.server_name=='loadtest.m.lsa0.cn' then
    local ok, err = redis:connect("10.30.63.74", 6379)
    local ok, err = redis:auth("Acctrue886")
    if not ok then
        ngx.say("failed to connect: ", err)
        return
    end
else
    local ok, err = redis:connect("10.30.146.201", 6379)
    local ok, err = redis:auth("Acctrue886")
    if not ok then
        ngx.say("failed to connect: ", err)
        return
    end
end


-- local code=ngx.var.arg_code
-- if not code then
--     ngx.log(ngx.ERR,ngx.var.request_uri)
--     err_page.err="没有码参数"
--     err_page:render()
--     return
-- end
-- local ok=redis:get(code)
-- if ok==ngx.null then
	--ngx.header.content_type = "text/html;charset=utf8;"
	--ngx.header.location = '/code/scan/'+code
	--ngx.req.set_uri('/code/scan/'+code, true)
	-- local reurl='/code/scan/'..code
	-- if ngx.var.arg_openid then
	-- 	reurl=reurl..ngx.var.arg_openid
	-- end
	-- ngx.redirect(reurl)
-- end

