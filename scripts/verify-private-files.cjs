// Status-only production check. No response bodies, authentication, or mutations.
const https = require('node:https');
const origin = new URL(process.argv[2] || 'https://sornaz.com');
const privatePath = process.argv[3];
if (origin.protocol !== 'https:' || origin.username || origin.password || origin.pathname !== '/' || origin.search || origin.hash) throw Error('Use an HTTPS origin without credentials or a path.');
if (!privatePath || !/^\/storage\/(?:chat|account-media|sessions|backups|logs|social-media|course-media)\//.test(privatePath) || /[?#\\\s]/.test(privatePath) || privatePath.split('/').includes('..')) throw Error('Provide the relative path of a known existing private storage file.');
function probe(method, path, headers = {}) {
    return new Promise((resolve, reject) => {
        const request = https.request(new URL(path, origin), {method,headers,timeout:15000}, response => {
            resolve({status:response.statusCode,cache:response.headers['cache-control'] || ''});
            response.destroy();
        });
        request.on('timeout',()=>request.destroy(Error('Request timed out')));
        request.on('error',reject);
        request.end();
    });
}
(async()=>{
    let failed=false;
    for (const [label,method,path,headers] of [
        ['private file HEAD','HEAD',privatePath,{}],
        ['private file GET','GET',privatePath,{}],
        ['private file with query','GET',privatePath+'?security-check='+Date.now(),{}],
        ['private file with range','GET',privatePath,{Range:'bytes=0-0'}],
    ]) {
        const result=await probe(method,path,headers);
        const pass=[403,404].includes(result.status);
        console.log(`${pass?'PASS':'FAIL'} ${label}: HTTP ${result.status}; cache=${result.cache || '(none)'}`);
        if(!pass)failed=true;
    }
    for(const path of ['/','/assets/theme/theme.css']) {
        const result=await probe('HEAD',path);
        const pass=result.status===200;
        console.log(`${pass?'PASS':'FAIL'} public ${path}: HTTP ${result.status}`);
        if(!pass)failed=true;
    }
    process.exitCode=failed?1:0;
})().catch(error=>{console.error(error.message);process.exitCode=1;});
