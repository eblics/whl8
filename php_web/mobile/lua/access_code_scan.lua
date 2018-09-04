local redis = require "resty.redis".new()
local ck = require "resty.cookie"
local cookie = ck:new()
local template = require "resty.template"
local wait_page = template.new "wait.html"
local err_page = template.new "error.html"
local ngx_in_limit=1000
local ngx_codes_len=0
local ngx_codes_limit=5000

ngx.header.content_type = "text/html;charset=utf8;";
local re_srv={["m.lsa0.cn"]="r-m5e1c7e6a2a727d4.redis.rds.aliyuncs.com",["test.m.lsa0.cn"]="10.30.146.201",["dev.m.lsa0.cn"]="10.45.225.217"}
local ok, err = redis:connect(re_srv[ngx.var.server_name], 6379)
local ok, err = redis:auth("Acctrue886")
if not ok then
    err_page.err="没有码参数"
    err_page:render()
    ngx.log(ngx.ERR,"failed to connect redis")
    return
end

----检查是否超出队列限制，如超出，则显示队列号
--if tonumber(cur_in)>ngx_in_limit then
--    redis:decr("ngx_current_in")
--    wait_page.number=cur_wait
--    wait_page:render()
--    return
--end
----总的等待数
--cur_wait, err = redis:incr("ngx_current_wait")
--if not cur_wait then
--    err_page.err=err
--    err_page.render()
--    return
--end

local url = ngx.var.request_uri 
local code=url:sub(12,-1)
local ei=string.find(code,'?')
if  ei then
    code=code:sub(0,ei-1)
end

if not code then
    err_page.err="没有码参数"
    err_page:render()
    return
end

----设置过期时间，60秒后清除当前处理中的队列，以防止请求堆积，无法接收新的请求
--local expire=redis:get('ngx_current_expire')
--if expire==ngx.null then
--    redis:set('ngx_current_expire',os.time()+60)
--end
--local expire=redis:get('ngx_current_expire')

--if tonumber(expire)<os.time() then
--    redis:zremrangebyrank('ngx_codes_set',0,ngx_in_limit)
--    redis:set('ngx_current_expire',os.time()+60)
--    redis:set('ngx_current_in',0)
--end

local rank=redis:zrank('ngx_codes_set',code)
if rank==ngx.null then
--    local cnt=redis:zcard('ngx_codes_set')
    local c=redis:zrevrangebyscore('ngx_codes_set','+inf','-inf','withscores','limit','0','1')[2]
    local max=0
    if c then 
        max=tonumber(c)+1 
    end
    redis:zadd('ngx_codes_set',max,code)
    redis:zadd('ngx_codes_expire',os.time(),code)
end

local rank=redis:zrank('ngx_codes_set',code)
if not rank then
    err_page.err="没有码参数"
    err_page:render()
    return
end
	
local cur_in = redis:incr("ngx_current_in")
if  tonumber(cur_in)>ngx_in_limit then
    wait_page.rank=tonumber(rank) + 1  ---ngx_in_limit+1
    wait_page:render()
    return
end
redis:set_keepalive(30000,100)

--local cur_in = redis:incr("ngx_current_in")
--if  tonumber(cur_in)>ngx_in_limit then
--    redis:decr('ngx_current_in')
--    wait_page.rank=tonumber(rank) + 1  ---ngx_in_limit+1
--    wait_page:render()
--    return
--end

--local ok, err = cookie:set({
--    key = "token",
--    value = code,
--    path = "/",
--    --domain = "lsa0.cn",
--    domain = ngx.var.server_name,
--    max_age = 50
--})

--for key, val in pairs(args) do
--    if key=="no" then
--        ngx.say("noaccess")
--        ngx.exit(ngx.HTTP_FORBIDDEN)
--    end
--    ngx.say("go on")
--end
