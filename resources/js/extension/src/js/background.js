require('./bg_components/listeners/checkerLkListener')
require('./bg_components/listeners/contentScriptListener')
require('./bg_components/listeners/onIconClickListener')
import startAlarm from './bg_components/startAlarm'
import {getTimeDifference} from './bg_components/getTimeDifference'
import refreshLkPage from './bg_components/refreshLkPage'
import removeNeedlessTabs from './bg_components/removeNeedlessTabs'

removeNeedlessTabs()
getTimeDifference(startAlarm)

refreshLkPage()
