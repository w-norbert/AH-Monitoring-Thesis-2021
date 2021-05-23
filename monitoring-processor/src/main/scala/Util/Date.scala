package Util

import java.time.{Instant, ZoneId}
import java.time.format.DateTimeFormatter

/**
 * Helper object for Date related operations
 */
object Date {
    /**
     * Returns the current date-time formatted to "yyyy-MM-dd HH:mm:ss"
     * @param secondsToSubtract Int number of seconds to subtract from current datetime
     *                          default is 0
     * @return The current Date-time
     */
    def getDateTimeNow(secondsToSubtract: Int = 0): String = {
        val limitDate =Instant
            .now()
            .minusSeconds(secondsToSubtract)
        val formatter =
            DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss")
                .withZone(ZoneId.systemDefault());
        return formatter.format(limitDate);
    }
}
