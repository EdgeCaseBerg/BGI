package bgi.models.dao.prototyping

import bgi.models.dao.UserDAO
import bgi.models.User

import scala.concurrent.{ExecutionContext, Future, future}

/** A Basic in-memory, Set based datastore to use while prototyping 
 * 
 * Good for mocking a DAL in tests or for testing services in isolation. 
 * Not really meant to be used for production system (hence the name)
 * And is not thread safe, id's should not be relied on to be unique and 
 * are subject to race conditions. 
 */
class ProtoUserDAO extends UserDAO {
	val store = scala.collection.mutable.Set[User]()
	var internalId : Long = 0

	def create(user: User)(implicit ec: ExecutionContext) : Future[Option[User]] = future {
		/* Kind of silly to have sync in here, but this is a prototyping class */
		synchronized {
			val userId = internalId
			store += user.copy(id = userId) 
			internalId += 1
			store.find(_.id == userId)
		}
	}

	def findById(id: Long)(implicit ec: ExecutionContext) : Future[Option[User]] = future {
		store.find(_.id == id)	
	}

	def remove(id: Long)(implicit ec: ExecutionContext) : Future[Boolean] = future {
		val toRemove = store.find(_.id == id)
		synchronized {
			toRemove.fold(false){ user => 
				store -= user
				!store.exists(_.id == id)
			}
		}
	}

	def update(user: User)(implicit ec: ExecutionContext) : Future[Boolean] = future {
		store.find(_.id == user.id).fold(false) { found => 
			synchronized {
				store -= found 
				store += user 
			}
			store.exists(_ == user)	
		}
	}

	def incrementLoginAttempt(user: User)(implicit ec: ExecutionContext) : Future[Boolean] = {
		update(user.copy(loginAttempts = user.loginAttempts + 1))
	}

	def resetLoginAttempts(user: User)(implicit ec: ExecutionContext) : Future[Boolean] = {
		update(user.copy(loginAttempts = 0))	
	}
}