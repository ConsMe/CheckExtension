// const axios = window.axios = require('axios')
require('./bg_components/listeners/checkerLkListener')
require('./bg_components/listeners/contentScriptListener')
require('./bg_components/listeners/onIconClickListener')
require('./bg_components/listeners/windowsOnRemoveListener')
import startAlarm from './bg_components/startAlarm'
import {getTimeDifference} from './bg_components/getTimeDifference'
import refreshLkPage from './bg_components/refreshLkPage'

getTimeDifference(startAlarm)

refreshLkPage()
