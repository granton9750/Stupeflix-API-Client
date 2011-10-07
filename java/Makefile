clean :
	find com | grep '.class$$' | xargs rm

compile: clean
	javac com/stupeflix/client/*.java ; 
	javac com/stupeflix/thirdparty/*.java ; 

run:
	javac Example.java ; 
	java -classpath ".:stupeflix.jar" Example


jarbuild: compile
	sh jarbuild.sh
